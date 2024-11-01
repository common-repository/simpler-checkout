<?php

namespace Simpler\Services;

use Exception;
use Simpler\Exceptions\BaseException;
use Simpler\Exceptions\InapplicableCouponException;
use Simpler\Exceptions\IndividuallySoldProductException;
use Simpler\Exceptions\InvalidCouponException;
use Simpler\Exceptions\OutOfStockProductException;
use Simpler\Exceptions\UnpurchasableProductException;
use Simpler\Exceptions\UnshippableCartException;
use Simpler\Exceptions\UnshippableLocationException;
use Simpler\Models\{Fee, Money, ProductAttribute, Quotation, QuotationRequest, QuotedProduct};

class QuotationService
{

    use CartHelper;

    /**
     * @var QuotationRequest
     */
    private $request;
    /**
     * Add product to cart exception
     *
     * @var BaseException
     */
    private $addProductToCartException;

    private function init()
    {
        $this->register_filters();
        $this->register_actions();
        $this->initialize_cart();
        WC()->session->set('chosen_payment_method', SIMPLERWC_PAYMENT_METHOD_SLUG);
    }

    /**
     *
     * @return Quotation[]
     * @throws Exception
     */
    public function quote(QuotationRequest $request): array
    {
        $this->init();
        $quotations    = [];
        $this->request = $request;

        if ($request->get_user_email()) {
            $user = \get_user_by('email', $request->get_user_email());
            if (is_a($user, 'WP_User')) {
                wp_set_current_user($user->ID);
            } else {
                wp_set_current_user(0); // override rest api access token holder
            }
        }

        foreach ($this->request->get_items() as $item) {
            $this->add_item_to_cart($item);
        }

        // apply coupon first to account for coupons that offer free shipping
        $discount = $this->maybe_apply_coupon();

        // Handle shipping
        if ($this->request->get_shipping_address() !== null) {
            foreach ($this->calculate_shipping_rates() as $shippingRate) {
                $quotations[] = (new Quotation())->set_shipping_rate($shippingRate);
            }
        }

        $this->add_payment_methods($quotations);

        if (empty($quotations)) {
            $quotations[] = (new Quotation());
        }

        $this->calculate_quotations_total($quotations, $discount);

        return $quotations;
    }

    /**
     * @return \WC_Shipping_Rate[]
     * @throws Exception|UnshippableCartException
     */
    protected function calculate_shipping_rates()
    {
        $customerProperties = $this->request->get_shipping_address()->to_customer_address_props();

        WC()->customer = new \WC_Customer(0, true);
        WC()->customer->set_props(apply_filters('simplerwc_customer_properties', $customerProperties));
        WC()->customer->save();
        WC()->cart->calculate_shipping();
        WC()->cart->calculate_totals();
        if (!WC()->cart->needs_shipping()) {
            throw new UnshippableCartException('no shipping methods available for product and address');
        }

        $packages = WC()->cart->get_shipping_packages();
        if (count($packages) > 1) {
            throw new UnshippableCartException('multiple shipping packages are not supported');
        }

        $shipping = WC()->shipping()->calculate_shipping($packages);

        if (isset($shipping[0]) && isset($shipping[0]['rates']) && !empty($shipping[0]['rates'])) {
            return apply_filters('simplerwc_shipping_rates', $shipping[0]['rates'], WC()->cart);
        }

        throw new UnshippableLocationException('cart can not be shipped to this address');
    }

    /**
     * Set total cents to quotation.
     *
     * @param  Quotation[]  $quotations
     * @param  int $discount_cents
     */
    private function calculate_quotations_total(array $quotations, int $discount_cents)
    {
        $products = $this->create_quoted_products();
        $cartSubtotal = WC()->cart->get_cart_contents_total() + WC()->cart->get_cart_contents_tax();

        foreach ($quotations as $quotation) {
            $quotation->set_products($products);

            $shippingCost = 0.0;
            $shippingTax = 0.0;
            $rate = $quotation->get_shipping_rate();

            if (!is_null($rate)) {
                $shippingCost += (float)$rate->get_cost();
                $shippingTax += array_sum($rate->get_taxes());
                WC()->session->set('chosen_shipping_methods', [$rate->get_id()]);
            }

            WC()->cart->calculate_totals();

            $quotation->set_shipping_cents(Money::to_cents($shippingCost));
            $quotation->set_shipping_tax_cents(Money::to_cents($shippingTax));

            $quotation->set_discount_cents($discount_cents);

            $fees = $this->create_fees();
            $feesCost = WC()->cart->get_fee_total() + WC()->cart->get_fee_tax();
            $quotation->set_fees($fees);

            $total = Money::to_cents($shippingCost + $shippingTax + $cartSubtotal + $feesCost);
            $quotation->set_total_cents($total);
        }
    }

    /**
     * @param  Quotation[]  $quotations
     *
     * @return int
     * @throws InapplicableCouponException
     * @throws InvalidCouponException
     */
    private function maybe_apply_coupon()
    {
        // TODO : this should happen per-quote since restrictions may apply per shipping method
        if (!$this->request->get_coupon_code()) {
            return 0;
        }

        if (!$this->coupon_exists()) {
            throw new InvalidCouponException('Supplied coupon does not exist');
        }

        $coupon = new \WC_Coupon(mb_strtolower($this->request->get_coupon_code()));
        $couponApplied = (new \WC_Discounts(WC()->cart))->apply_coupon($coupon);
        if (\is_wp_error($couponApplied)) {
            throw new InapplicableCouponException(json_encode(WC()->session->get('wc_notices', [])));
        }

        WC()->cart->apply_coupon($couponCode = $coupon->get_code());
        return Money::to_cents(WC()->cart->get_coupon_discount_amount($couponCode) + WC()->cart->get_coupon_discount_tax_amount($couponCode));
    }

    /**
     * Create an array of QuotedProducts based on cart's contents.
     *
     * @return QuotedProduct[]
     */
    private function create_quoted_products(): array
    {
        $products = [];
        foreach (WC()->cart->get_cart() as $lineItem) {
            $attributes = [];
            if (!empty($pairs = $lineItem['variation'])) {
                foreach ($pairs as $key => $value) {
                    $attributes[] = new ProductAttribute($key, $value);
                }
            }

            $products[] = new QuotedProduct(
                $lineItem['variation_id'] ?: $lineItem['product_id'],
                $lineItem['quantity'],
                $lineItem['line_total'],
                $lineItem['line_tax'],
                $lineItem['line_total'] + $lineItem['line_tax'],
                $lineItem['line_subtotal'],
                $lineItem['line_subtotal_tax'],
                $lineItem['line_subtotal'] + $lineItem['line_subtotal_tax'],
                $attributes
            );
        }

        return $products;
    }

    /**
     * Create an array of Fees based on cart's contents.
     *
     * @return Fee[]
     */
    private function create_fees(): array
    {
        $fees = [];
        foreach (WC()->cart->get_fees() as $fee) {
            $fees[] = new Fee(
                $fee->id,
                $fee->name,
                $fee->amount,
                $fee->total,
                $fee->tax
            );
        }

        return $fees;
    }

    /**
     * Determine whether given coupon exists or not.
     *
     * @return bool
     */
    private function coupon_exists(): bool
    {
        return wc_get_coupon_id_by_code($this->request->get_coupon_code()) > 0;
    }

    /**
     * Register WordPress filters
     */
    private function register_filters()
    {
        add_filter(
            'woocommerce_cart_product_cannot_add_another_message',
            [$this, 'individually_sold_product_exception']
        );
        add_filter(
            'woocommerce_cart_product_cannot_be_purchased_message',
            [$this, 'unpurchasable_product_exception']
        );
        add_filter(
            'woocommerce_cart_product_out_of_stock_message',
            [$this, 'out_of_stock_product_exception']
        );
        add_filter(
            'woocommerce_cart_product_not_enough_stock_message',
            [$this, 'out_of_stock_product_exception']
        );
        add_filter(
            'woocommerce_cart_product_not_enough_stock_already_in_cart_message',
            [$this, 'out_of_stock_product_exception']
        );
    }

    /**
     * Register WordPress filters
     */
    private function register_actions()
    {
        // Destroy session, and the DB record that comes with it, that was automatically created during WC_Cart instantiation.
        add_action('shutdown', function () {
            WC()->session->destroy_session();
        });
    }

    /**
     * Thrown when a product that is sold individually already exists in cart.
     *
     * @param $message
     *
     * @return string
     */
    public function individually_sold_product_exception($message)
    {
        $this->addProductToCartException = new IndividuallySoldProductException($message);
        return $message;
    }

    /**
     * Thrown when a product can not be purchased.
     *
     * @param $message
     *
     * @return string
     */
    public function unpurchasable_product_exception($message)
    {
        $this->addProductToCartException = new UnpurchasableProductException($message);
        return $message;
    }

    /**
     * Thrown when a product:
     * 1. is out of stock
     * 2. doesn't have enough stock
     * 3. doesn't have enough stock accounting for what's already in-cart
     *
     * @param $message
     *
     * @return string
     */
    public function out_of_stock_product_exception($message)
    {
        $this->addProductToCartException = new OutOfStockProductException(wc_clean($message));
        return $message;
    }

    /**
     * @param  Quotation[]  $quotations
     *
     * @return void
     */
    private function add_payment_methods(array $quotations)
    {
        foreach ($quotations as $quotation) {
            $quotation->set_payment_methods(
                // Quotation is cloned to prevent alteration in callbacks.
                apply_filters('simplerwc_quotation_payment_method', [], clone $quotation) ?: []
            );
        }
    }
}
