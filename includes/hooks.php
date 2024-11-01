<?php

use Simpler\Services\OrderAttributionService;

add_action('init', 'simplerwc_woocommerce_order_created');

function simplerwc_woocommerce_order_created()
{

    if (!isset($_GET['simpler_order_created'])) {
        return;
    }

    $redirect_url = wc_get_endpoint_url('order-received', null, wc_get_checkout_url());

    $order_id = absint($_GET['simpler_order_created']);
    $order = wc_get_order($order_id);

    if ($order) {
        wc_empty_cart();

        if (get_option('simplerwc_support_woo_order_attribution', false) && !isset($_GET['dnt'])) {
            OrderAttributionService::save_attribution_data($order, $_COOKIE);
        }

        $user = $order->get_user();
        if ($user) {
            wc_set_customer_auth_cookie($user->ID);
        }
        $redirect_url = $order->get_checkout_order_received_url();
    }

    wp_safe_redirect($redirect_url);
    exit;
}

add_action('woocommerce_order_refunded', 'simplerwc_woocommerce_order_refunded', 10, 2);

/**
 * Send a webhook to Simpler when an order is refunded.
 *
 * @param $order_id
 */
function simplerwc_woocommerce_order_refunded($order_id, $refund_id)
{
    $order = wc_get_order($order_id);
    $refund = wc_get_order($refund_id);

    if ($order->get_payment_method() != SIMPLERWC_PAYMENT_METHOD_SLUG) {
        return;
    }

    $request_body = ['order_id' => $order_id];
    if ($order->get_remaining_refund_amount() > 0.0) {
        $request_body['amount'] = Simpler\Models\Money::to_cents($refund->get_amount());
    }

    wp_remote_post(simplerwc_get_refund_uri(), [
        'body'     => json_encode($request_body),
        'headers'  => [
            'Authorization' => 'Basic ' . base64_encode(\get_option('simpler_api_key') . ':' . \get_option('simpler_api_secret'))
        ],
        'blocking' => false
    ]);
}
