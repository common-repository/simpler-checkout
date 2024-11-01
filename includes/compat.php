<?php

// https://woocommerce.com/products/free-gifts-for-woocommerce/
use Simpler\Models\Money;
use Simpler\Models\OrderRequest;
use Simpler\Models\PaymentMethod;
use Simpler\Models\Quotation;

if (function_exists('fgf_is_plugin_active') && \fgf_is_plugin_active()) {
    function simplerwc_fgf_after_add_to_cart()
    {
        // give fgf the hook to add automatic gifts to cart
        do_action('woocommerce_before_mini_cart');
        foreach (WC()->cart->get_cart_contents() as $key => $item) {
            if (array_key_exists('fgf_gift_product', $item)) {
                foreach (WC()->cart->get_cart_contents() as $k => $v) {
                    if ($key != $k && $v['product_id'] == $item['product_id'] && $v['variation_id'] == $item['variation_id']) {
                        WC()->cart->remove_cart_item($k);
                    }
                }
            }
        }
    }
    add_action('simplerwc_after_add_to_cart', 'simplerwc_fgf_after_add_to_cart');
}

// https://woocommerce.com/products/local-pickup-plus/
function simplerwc_compat_wc_local_pickup_plus()
{
    if (class_exists('WC_Local_Pickup_Plus')) {
        /**
         * @param \WC_Order $order
         * @param OrderRequest $order_request
         */
        function simplerwc_local_pickup_plus_after_set_checkout_data($order, $order_request)
        {
            $pickupId = $order_request->get_order()->get_shipping()->get_pickup_location_id();
            if (!$pickupId || !class_exists('WC_Local_Pickup_Plus')) {
                return;
            }

            $location = new \WC_Local_Pickup_Plus_Pickup_Location($pickupId);
            $order_items = new \WC_Local_Pickup_Plus_Order_Items();
            foreach ($order->get_items('shipping') as $item) {
                $order_items->set_order_item_pickup_location($item, $location);
            }
        }
        add_action('simplerwc_after_set_checkout_data', 'simplerwc_local_pickup_plus_after_set_checkout_data', 10, 2);
    }
}
add_action('init', 'simplerwc_compat_wc_local_pickup_plus');

/**
 * @param  Quotation  $quotation
 * @param  array      $paymentMethods
 *
 * @return PaymentMethod[]
 */
function simplerwc_compat_wc_smart_cod_quote(array $paymentMethods, Quotation $quotation)
{
    $availablePaymentMethods = WC()->payment_gateways()->get_available_payment_gateways();
    // COD is not configured
    if (!isset($availablePaymentMethods['cod'])) {
        return $paymentMethods;
    }

    $cod = $availablePaymentMethods['cod'];

    // COD is disabled
    if ($availablePaymentMethods['cod']->enabled != 'yes') {
        return $paymentMethods;
    }
    // COD isn't enabled for quote's shipping method (e.g. local pickup or BoxNow)
    if (count($cod->enable_for_methods) > 0 && !in_array($quotation->get_shipping_rate()->get_method_id(), $cod->enable_for_methods)) {
        return $paymentMethods;
    }

    $codCost = 0;
    if (class_exists('Wc_Smart_Cod_Admin')) {
        $codCost = $cod->settings['extra_fee'];
    } else if (class_exists('Pay4Pay')) {
        $codCost = $cod->settings['pay4pay_charges_fixed'];
    }

    $codCost = apply_filters('simplerwc_cod_cost_cents', $codCost);

    $paymentMethods[] = new PaymentMethod(
        $cod->id,
        $cod->title,
        PaymentMethod::COD,
        Money::to_cents($codCost),
        null,
        null
    );

    return $paymentMethods;
}

add_filter('simplerwc_quotation_payment_method', 'simplerwc_compat_wc_smart_cod_quote', 10, 2);

/**
 * @param  Closure[]     $closures
 * @param  OrderRequest  $orderRequest
 *
 * @return array
 */
function simplerwc_compat_wc_smart_cod_order(array $closures, OrderRequest $orderRequest): array
{
    if (
        !($paymentMethod = $orderRequest->get_order()->get_payment_method())
        || $paymentMethod->getType() != PaymentMethod::COD
        || !class_exists('Wc_Smart_Cod_Admin')
    ) {
        return $closures;
    }

    $availablePaymentGateways = WC()->payment_gateways()->get_available_payment_gateways();
    $paymentGateway = $availablePaymentGateways[$orderRequest->get_order()->get_payment_method()->getId()] ?? null;

    if ($paymentGateway instanceof \Wc_Smart_Cod_Admin) {
        $closures[] = function () use ($paymentGateway, $paymentMethod) {
            WC()->cart->add_fee(
                $paymentMethod->getName() ?: $paymentGateway->title,
                $paymentMethod->getTotalCents() != null ? Money::from_cents($paymentMethod->getTotalCents()) : $paymentGateway->settings['extra_fee']
            );
        };
    }
    return $closures;
}

add_filter('simplerwc_order_fees', 'simplerwc_compat_wc_smart_cod_order', 10, 2);

// iThemeland Free Gifts : https://ithemelandco.com/plugins/free-gifts-for-woocommerce/
function simplerwc_compat_ithemeland_free_gifts_add_to_cart()
{
    if (!class_exists('iThemeland_front_order')) {
        return;
    }
    $ithemeland = new iThemeland_front_order();
    $ithemeland->check_session_gift();
    $ithemeland->pw_add_free_gifts();
}

function simplerwc_compat_ithemeland_free_gifts_ignore($value, $cart_item)
{
    if (isset($cart_item['it_free_gift'])) {
        return true;
    }
    return $value;
}

add_action('simplerwc_after_add_to_cart', 'simplerwc_compat_ithemeland_free_gifts_add_to_cart', 10, 0);
add_filter('simplerwc_button_should_ignore_cart_item', 'simplerwc_compat_ithemeland_free_gifts_ignore', 10, 2);

//WC Pickup Store https://wordpress.org/plugins/wc-pickup-store
function simplerwc_compat_wc_pickup_store_set_store_name($order, $request)
{
    if (!function_exists('wps_stores_fields')) {
        return;
    }

    if (!($order->has_shipping_method('wc_pickup_store'))) {
        return;
    }

    $location = $request->get_order()->get_shipping()->get_pickup_location_id();
    if ($location) {
        $order->add_meta_data('_shipping_pickup_stores', $location);
    }
}
add_action('simplerwc_after_set_checkout_data', 'simplerwc_compat_wc_pickup_store_set_store_name', 10, 2);
