<?php

namespace Simpler\Admin\Http\Controllers;

class ButtonController extends Controller
{
    public static function register()
    {
        register_setting('simpler_button', 'simpler_auto_render_product_button', [
            'type'    => 'number',
            'default' => 1,
        ]);

        register_setting('simpler_button', 'simplerwc_product_button_placement', [
            'type'    => 'string',
            'default' => 'woocommerce_after_add_to_cart_quantity',
        ]);

        register_setting('simpler_button', 'simpler_auto_render_cart_button', [
            'type'    => 'number',
            'default' => 1,
        ]);

        register_setting('simpler_button', 'simplerwc_cart_page_button_placement', [
            'type'    => 'string',
            'default' => 'woocommerce_proceed_to_checkout',
        ]);

        register_setting('simpler_button', 'simplerwc_auto_render_checkout_page_button', [
            'type' => 'number',
            'default' => 1,
        ]);

        register_setting('simpler_button', 'simplerwc_checkout_page_button_placement', [
            'type'    => 'string',
            'default' => 'woocommerce_checkout_before_customer_details',
        ]);

        register_setting('simpler_button', 'simplerwc_auto_render_minicart_button', [
            'type'    => 'number',
            'default' => 1,
        ]);

        register_setting('simpler_button', 'simplerwc_minicart_button_placement', [
            'type' => 'string',
            'default' => 'woocommerce_widget_shopping_cart_before_buttons'
        ]);

        register_setting('simpler_button', 'simplerwc_show_cards_notice', [
            'type' => 'number',
            'default' => 1,
        ]);

        register_setting('simpler_button', 'simplerwc_excluded_user_roles', [
            'type' => 'array',
            'default' => []
        ]);
    }

    public function button()
    {
        $this->render('settings._button', [
            'autoRenderProductButton'            => checked(1, get_option('simpler_auto_render_product_button'), false),
            //https://www.businessbloomer.com/woocommerce-visual-hook-guide-single-product-page/
            'productButtonPlacement'             => get_option('simplerwc_product_button_placement'),
            'autoRenderCartButton'               => checked(1, get_option('simpler_auto_render_cart_button'), false),
            'cartPageButtonPlacement'            => get_option('simplerwc_cart_page_button_placement'),
            'autoRenderCheckoutPageButton'       => checked(1, get_option('simplerwc_auto_render_checkout_page_button'), false),
            'checkoutPageButtonPlacement'        => get_option('simplerwc_checkout_page_button_placement'),
            'autoRenderMinicartButton'           => checked(
                1,
                get_option('simplerwc_auto_render_minicart_button'),
                false
            ),
            'minicartButtonPlacement' => get_option('simplerwc_minicart_button_placement'),
            'showCardsUnderButton'  => checked(
                1,
                get_option('simplerwc_show_cards_notice'),
                false
            ),
            'excludedRoles' => $this->excludedRolesDropdown()
        ]);
    }

    private function excludedRolesDropdown()
    {
        global $wp_roles;
        $exclude = get_option('simplerwc_excluded_user_roles') ?: [];
        return array_map(function ($el) use ($exclude) {
            return [
                'name' => $el,
                'selected' => selected(true, in_array($el, $exclude, true), false)
            ];
        }, array_keys($wp_roles->roles));
    }
}
