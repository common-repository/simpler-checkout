<?php

add_filter('plugins_loaded', 'simplerwc_register_payment_gateway');
function simplerwc_register_payment_gateway()
{
    class WC_Simpler_Checkout_Gateway extends \WC_Payment_Gateway
    {
        public function __construct()
        {
            $this->id = SIMPLERWC_PAYMENT_METHOD_SLUG;
            $this->method_title = 'Simpler Checkout';
            $this->method_description = 'This is a stub gateway implementation to ensure compatibility with WooCommerce. No payments are processed through the gateway, all payment operations are executed in the Simpler systems.';
            $this->title = 'Simpler Checkout Payment Gateway';
            $this->enabled = true;

            $this->init_form_fields();
            $this->init_settings();
        }
    }
}

add_filter('woocommerce_payment_gateways', 'simplerwc_add_payment_gateway', 10, 1);
function simplerwc_add_payment_gateway($gateways)
{
    $gateways[] = 'WC_Simpler_Checkout_Gateway';
    return $gateways;
}
