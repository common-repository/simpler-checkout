<?php

use Simpler\Http\Controllers\{AboutController, OrderController, QuotationController, ProductController};
use Simpler\Services\QuotationService;

add_action('rest_api_init', 'simplerwc_register_routes');
add_action('rest_api_init', 'simplerwc_rest_api_includes');

function simplerwc_register_routes()
{
    $controllers = [
        new OrderController(),
        new AboutController(),
        new QuotationController(new QuotationService()),
        new ProductController(),
    ];
    foreach ($controllers as $controller) {
        register_rest_route(
            $controller->get_namespace(),
            $controller->get_route(),
            [
                'methods'             => $controller->get_method(),
                'callback'            => [$controller, 'handle'],
                'permission_callback' => [$controller, 'get_permission_callback'],
            ]
        );
    }
}

function simplerwc_rest_api_includes()
{
    // Fixes https://github.com/woocommerce/woocommerce/issues/27157
    if (empty(WC()->cart)) {
        WC()->frontend_includes();
        wc_load_cart();
    }
}
