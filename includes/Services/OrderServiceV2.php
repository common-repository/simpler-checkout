<?php

namespace Simpler\Services;

use Simpler\Exceptions\{InapplicableCouponException, InvalidCouponException, InvalidProductException};
use Simpler\Models\CartItem;
use Simpler\Models\Money;
use Simpler\Models\OrderRequest;

class OrderServiceV2
{

    private $addFeeActionClosure;

    /**
     * @throws InvalidCouponException
     * @throws InapplicableCouponException
     * @throws \WC_Data_Exception
     * @throws InvalidProductException
     */
    public function create_order($user_id, OrderRequest $order_request)
    {
        $existing = $this->get_order_by_meta('simpler_cart_id', $order_request->get_order()->get_simpler_cart_id());
        if (count($existing) > 0) {
            return $existing[0];
        }

        add_filter('woocommerce_persistent_cart_enabled', '__return_false');
        WC()->session  = new \WC_Session_Handler();
        WC()->cart     = new \WC_Cart();
        WC()->customer = new \WC_Customer($user_id);
        $order         = new \WC_Order(0);

        $order = $this->assign_shipping_address($order, $order_request);
        WC()->session->set('chosen_payment_method', SIMPLERWC_PAYMENT_METHOD_SLUG);
        if ($order_request->get_order()->get_shipping() != NULL) {
            WC()->session->set('chosen_shipping_methods', [$order_request->get_order()->get_shipping()->get_rate_id()]);
        }

        $order->set_customer_id($user_id);
        $order->update_meta_data(
            'simpler_cart_id',
            $order_request->get_order()->get_simpler_cart_id()
        );

        foreach ($order_request->get_order()->get_cart() as $item) {
            $this->add_item_to_cart($item);
        }

        $this->apply_coupon($order_request->get_order()->get_coupon());
        $this->apply_discount($order_request->get_order()->get_simpler_discount());
        WC()->cart->calculate_totals();

        WC()->checkout()->create_order_line_items($order, WC()->cart);
        WC()->checkout()->create_order_coupon_lines($order, WC()->cart);
        WC()->checkout()->create_order_fee_lines($order, WC()->cart);
        $order = $this->create_shipping_line_item(
            $order,
            $order_request->get_order()->get_shipping()
        );

        $order->set_payment_method(SIMPLERWC_PAYMENT_METHOD_SLUG);
        $order->set_payment_method_title(SIMPLERWC_PAYMENT_METHOD_SLUG);
        $order->set_status(\apply_filters('woocommerce_default_order_status', 'processing'));

        do_action('woocommerce_checkout_create_order', $order, []);
        $order_id = $order->save();
        do_action('woocommerce_checkout_update_order_meta', $order_id, []);
        $order->calculate_totals();

        if ($this->addFeeActionClosure != NULL) {
            remove_action('woocommerce_cart_calculate_fees', $this->addFeeActionClosure);
        }
        return $order;
    }

    private function get_order_by_meta($key, $val)
    {
        return \wc_get_orders([
            'limit'      => 1,
            'meta_key'   => $key,
            'meta_value' => $val,
        ]);
    }

    /**
     * @param                $wc_order
     * @param  OrderRequest  $request
     *
     * @return \WC_Order
     */
    private function assign_shipping_address($wc_order, OrderRequest $request)
    {
        $user = $request->get_user();
        if ($address = $request->get_ship_to()) {
            $wc_order->set_address($address->to_address_props(), 'shipping');
            $wc_order->set_address($address->to_address_props(), 'billing');
            WC()->customer->set_props($address->to_customer_address_props());
        } else {
            $wc_order->set_billing_first_name($user->get_first_name());
            $wc_order->set_billing_last_name($user->get_last_name());
        }

        return $wc_order;
    }

    /**
     * @throws \WC_Data_Exception
     * @return \WC_Order
     */
    private function create_shipping_line_item($wc_order, $shipping)
    {
        if (is_null($shipping)) {
            return $wc_order; //nothing to do
        }
        $line_item = new \WC_Order_Item_Shipping();
        $line_item->set_instance_id($shipping->get_instance_id());
        $line_item->set_method_id($shipping->get_method_id());
        $line_item->set_method_title($shipping->get_method_title());
        $line_item->set_total(\wc_format_decimal($shipping->get_cost()));

        $wc_order->add_item($line_item);

        return $wc_order;
    }

    /**
     * @throws InvalidProductException
     * @throws \Exception
     */
    private function add_item_to_cart(CartItem $item)
    {
        $productAdded = WC()->cart->add_to_cart(
            $item->get_product_id(),
            $item->get_quantity(),
            null,
            $item->get_attributes_array()
        );
        if (is_bool($productAdded) && !$productAdded) {
            throw new InvalidProductException(json_encode(WC()->session->get('wc_notices')));
        }
    }

    /**
     * @throws InvalidCouponException
     * @throws InapplicableCouponException
     */
    private function apply_coupon($coupon)
    {
        if (!$coupon) {
            return;
        }

        if (!$this->coupon_exists($coupon)) {
            throw new InvalidCouponException('Supplied coupon does not exist');
        }

        $applied = WC()->cart->apply_coupon(mb_strtolower($coupon));
        if (!$applied) {
            throw new InapplicableCouponException(json_encode(WC()->session->get('wc_notices')));
        }
    }

    private function apply_discount($amount)
    {
        if (!$amount) {
            return;
        }
        $this->addFeeActionClosure = function () use ($amount) {
            $net = $amount;
            if (wc_tax_enabled()) {
                $taxes = \WC_Tax::calc_tax($amount, \WC_Tax::get_rates('', WC()->cart->get_customer()), true);
                $tax   = (is_array($taxes) && ! empty($taxes)) ? array_values($taxes)[0] : 0;
                $net   = $amount - $tax;
            }
            WC()->cart->add_fee('Simpler Discount', $net * -1.0);
        };
        add_action('woocommerce_cart_calculate_fees', $this->addFeeActionClosure);
    }

    private function coupon_exists($coupon): bool
    {
        return wc_get_coupon_id_by_code($coupon) > 0;
    }
}
