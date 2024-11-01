<?php

namespace Simpler\Services;

use Simpler\Exceptions\{InapplicableCouponException, InvalidCouponException, InvalidProductException};
use Simpler\Models\OrderRequest;

class OrderService
{
    use CartHelper;

    private $addFeeActionClosure;
    private $addFeeTaxesActionClosure;

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

        wp_set_current_user($user_id);
        $this->initialize_cart();
        WC()->customer = new \WC_Customer($user_id);
        $order         = new \WC_Order(0);

        $order = $this->assign_shipping_address($order, $order_request);
        $order->set_customer_id($user_id);
        $order->update_meta_data(
            'simpler_cart_id',
            $order_request->get_order()->get_simpler_cart_id()
        );
        $paymentMethodId = $this->get_payment_method_id($order_request);
        WC()->session->set('chosen_payment_method', $paymentMethodId);

        foreach ($order_request->get_order()->get_cart() as $item) {
            $this->add_item_to_cart($item);
        }

        $this->apply_coupon($order_request->get_order()->get_coupon());

        if ($order_request->get_order()->get_shipping() != NULL) {
            $chosen_shipping_methods = [];
            foreach (\WC()->shipping->get_packages() as $package_key => $package) {
                $chosen_shipping_methods[$package_key] = $order_request->get_order()->get_shipping()->get_rate_id();
            }
            \WC()->session->set('chosen_shipping_methods', $chosen_shipping_methods);
            if ($lockerId = $order_request->get_order()->get_shipping()->get_box_now_locker_id()) {
                // Box Now plugin expects this parameter to be present on the super global $_POST for its hooks to be triggered.
                $_POST['boxnow_locker_id'] = $lockerId; //boxnow <2.0
                $_POST['_boxnow_locker_id'] = $lockerId; //boxnow >=2.0
            }
        }

        $this->apply_discount($order_request->get_order()->get_simpler_discount());
        $closures = apply_filters('simplerwc_order_fees', [], $order_request);
        foreach ($closures as $closure) {
            add_action('woocommerce_cart_calculate_fees', $closure);
        }
        \WC()->cart->calculate_totals();

        // remove fee calculation actions now that they've ran
        if ($this->addFeeActionClosure != NULL) {
            remove_action('woocommerce_cart_calculate_fees', $this->addFeeActionClosure, 10, 1);
        }
        if ($this->addFeeTaxesActionClosure != NULL) {
            remove_filter('woocommerce_cart_totals_get_fees_from_cart_taxes', $this->addFeeTaxesActionClosure, 10, 2);
        }
        foreach ($closures as $closure) {
            remove_action('woocommerce_cart_calculate_fees', $closure);
        }

        \WC()->checkout()->set_data_from_cart($order);
        do_action('simplerwc_after_set_checkout_data', $order, $order_request);
        $order->set_payment_method($paymentMethodId);
        if ($paymentMethodId == SIMPLERWC_PAYMENT_METHOD_SLUG) {
            $order->set_payment_method_title(SIMPLERWC_PAYMENT_METHOD_SLUG);
        }
        $order->set_status(\apply_filters('woocommerce_default_order_status', 'processing'));

        do_action('woocommerce_checkout_create_order', $order, []);
        $order_id = $order->save();
        do_action('woocommerce_checkout_update_order_meta', $order_id, []);
        do_action('woocommerce_checkout_order_created', $order);
        do_action('simplerwc_order_created', $order, $order_request);
        if ($paymentMethodId == SIMPLERWC_PAYMENT_METHOD_SLUG) {
            do_action('woocommerce_payment_complete', $order_id);
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
            WC()->customer->set_props(apply_filters('simplerwc_customer_properties', $address->to_customer_address_props()));
        } else {
            $wc_order->set_billing_first_name($user->get_first_name());
            $wc_order->set_billing_last_name($user->get_last_name());
        }

        return $wc_order;
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

        $tax = $this->calculate_discount_tax(WC()->cart, $amount);
        $this->addFeeActionClosure = function () use ($amount, $tax) {
            WC()->cart->add_fee('Simpler Discount', ($amount - $tax) * -1.0);
        };
        $this->addFeeTaxesActionClosure = function ($taxes, $fee) use ($tax) {
            if ($fee->object->name == 'Simpler Discount') {
                $fee->taxes = [- ($tax * 100)];
                return $fee->taxes;
            }
            return $taxes;
        };

        add_action('woocommerce_cart_calculate_fees', $this->addFeeActionClosure, 10, 1);
        add_filter('woocommerce_cart_totals_get_fees_from_cart_taxes', $this->addFeeTaxesActionClosure, 10, 2);
    }

    private function calculate_discount_tax($cart, $discount): float
    {
        if (!wc_tax_enabled()) {
            return 0.0;
        }
        $taxes = [];
        $gross = floatval($cart->get_subtotal()) + floatval($cart->get_shipping_total()) + floatval($cart->get_subtotal_tax()) + floatval($cart->get_shipping_tax());
        $proportion = floatval($cart->get_total_tax()) / $gross;
        $taxes = \wc_array_merge_recursive_numeric($taxes, \WC_Tax::calc_tax($discount * (1 - $proportion), \WC_Tax::get_rates(''), true));
        return array_sum($taxes);
    }

    private function coupon_exists($coupon): bool
    {
        return wc_get_coupon_id_by_code($coupon) > 0;
    }

    private function get_payment_method_id(OrderRequest $order_request): string
    {
        return $order_request->get_order()->get_payment_method()
            ? $order_request->get_order()->get_payment_method()->getId()
            : SIMPLERWC_PAYMENT_METHOD_SLUG;
    }
}
