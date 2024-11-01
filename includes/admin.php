<?php

use Simpler\Admin\Http\Controllers\{
    ButtonController,
    ConfigurationController,
    HomeController,
    ShortcodesController
};
use Simpler\Services\BladeFactory;
use Simpler\Services\IntegrationService;

add_action('init', 'simplerwc_load_settings', 1);
add_action('admin_init', 'simplerwc_load_settings_form');
add_action('admin_menu', 'simplerwc_init_simpler_admin_dashboard');

function simplerwc_load_settings()
{
    ConfigurationController::register();
    ButtonController::register();

    register_setting('simpler_styling', 'simpler_product_button_custom_style', [
        'type' => 'string',
    ]);

    register_setting('simpler_styling', 'simpler_mini_cart_button_custom_style', [
        'type' => 'string',
    ]);

    register_setting('simpler_styling', 'simpler_cart_page_button_custom_style', [
        'type' => 'string',
    ]);

    register_setting('simpler_styling', 'simpler_checkout_page_button_custom_style', [
        'type' => 'string',
    ]);
}

function simplerwc_load_settings_form()
{
    add_settings_section(
        'simpler_configuration_settings',
        '',
        'simplerwc_echo_configuration_settings',
        'simpler_management'
    );

    add_settings_section(
        'simpler_configuration_status',
        '',
        'simplerwc_echo_integration_status',
        'simpler_management'
    );

    add_settings_section(
        'simpler_custom_styles',
        'Simpler Button Custom Styling (Advanced)',
        'simplerwc_custom_styles',
        'simpler_styling'
    );

    add_settings_field(
        'simpler_product_button_custom_style',
        'Product Page',
        'simplerwc_echo_simpler_product_button_custom_style',
        'simpler_styling',
        'simpler_custom_styles'
    );

    add_settings_field(
        'simpler_mini_cart_button_custom_style',
        'Mini Cart',
        'simplerwc_echo_simpler_mini_cart_button_custom_style',
        'simpler_styling',
        'simpler_custom_styles'
    );

    add_settings_field(
        'simpler_cart_page_button_custom_style',
        'Cart Page',
        'simplerwc_echo_simpler_cart_page_button_custom_style',
        'simpler_styling',
        'simpler_custom_styles'
    );

    add_settings_field(
        'simpler_checkout_page_button_custom_style',
        'Checkout Page',
        'simplerwc_echo_simpler_checkout_page_button_custom_style',
        'simpler_styling',
        'simpler_custom_styles'
    );

    add_settings_section(
        'simpler_product_button',
        '',
        'simplerwc_echo_button',
        'simpler_button'
    );

    add_settings_section(
        'simpler_shortcodes',
        '',
        'simplerwc_echo_shortcodes_settings',
        'simpler_shortcodes'
    );
}

function simplerwc_custom_styles()
{
    echo '<hr class="divider" />';
}

function simplerwc_echo_shortcodes_settings()
{
    (new ShortcodesController(BladeFactory::forAdmin()))->shortcodes();
}

function simplerwc_echo_configuration_settings()
{
    (new ConfigurationController(BladeFactory::forAdmin(), new IntegrationService()))->settings();
}

function simplerwc_echo_integration_status()
{
    (new ConfigurationController(BladeFactory::forAdmin(), new IntegrationService()))->integrationStatus();
}

function simplerwc_echo_button()
{
    (new ButtonController(BladeFactory::forAdmin()))->button();
}

function simplerwc_echo_simpler_product_button_custom_style()
{
    echo '<textarea name="simpler_product_button_custom_style" id="simpler_product_button_custom_style" cols=28 rows=10>' . esc_textarea(get_option('simpler_product_button_custom_style')) . '</textarea>';
}

function simplerwc_echo_simpler_mini_cart_button_custom_style()
{
    echo '<textarea name="simpler_mini_cart_button_custom_style" id="simpler_mini_cart_button_custom_style" cols=28 rows=10>' . esc_textarea(get_option('simpler_mini_cart_button_custom_style')) . '</textarea>';
}

function simplerwc_echo_simpler_cart_page_button_custom_style()
{
    echo '<textarea name="simpler_cart_page_button_custom_style" id="simpler_cart_page_button_custom_style" cols=28 rows=10>' . esc_textarea(get_option('simpler_cart_page_button_custom_style')) . '</textarea>';
}

function simplerwc_echo_simpler_checkout_page_button_custom_style()
{
    echo '<textarea name="simpler_checkout_page_button_custom_style" id="simpler_checkout_page_button_custom_style" cols=28 rows=10>' . esc_textarea(get_option('simpler_checkout_page_button_custom_style')) . '</textarea>';
}

function simplerwc_init_simpler_admin_dashboard()
{
    (new HomeController(BladeFactory::forAdmin()))->home();
}

add_filter('plugin_action_links_' . SIMPLERWC_PLUGIN_BASENAME, 'simplerwc_add_plugin_page_settings_link');

function simplerwc_add_plugin_page_settings_link($links)
{
    return array_merge([
        '<a href="' . admin_url('options-general.php?page=simpler_management') . '">' . __('Settings') . '</a>'
    ], $links);
}
