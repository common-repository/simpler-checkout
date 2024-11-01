<?php

/**
 * Check whether WooCommerce is active.
 *
 * @return bool
 */
function simplerwc_is_woocommerce_active()
{
    if (!function_exists('is_plugin_active')) {
        require_once ABSPATH . '/wp-admin/includes/plugin.php';
    }

    return is_plugin_active('woocommerce/woocommerce.php');
}

/**
 * Add hook for admin notice caused by missing WooCommerce plugin
 */
function simplerwc_admin_notice_woocommerce_is_missing()
{
    add_action(
        'admin_notices',
        'simplerwc_display_admin_notice_for_missing_woocommerce'
    );
}

/**
 * Display the error message when WooCommerce plugin is missing.
 */
function simplerwc_display_admin_notice_for_missing_woocommerce()
{
    printf(
        '<div class="notice notice-error"><p>%s</p></div>',
        'Simpler Checkout requires an active WooCommerce installation.'
    );
}

/**
 * Check if plugin required configuration is set
 */
function simplerwc_check_simpler_configured()
{
    return get_option('simpler_api_key') && get_option('simpler_api_secret');
}

/**
 * Add hook for admin notice caused by incomplete Simpler configuration
 */
function simplerwc_admin_notice_incomplete_configuration()
{
    add_action(
        'admin_notices',
        'simplerwc_display_admin_notice_incomplete_configuration'
    );
}

/**
 * Display error message when API Key & Secret is not configured.
 */
function simplerwc_display_admin_notice_incomplete_configuration()
{
    printf(
        '<div class="notice notice-error"><p>%s</p></div>',
        'Simpler Checkout <a href="/wp-admin/options-general.php?page=simpler_management&tab=simpler_management">API Key & Secret</a> are not configured, quick buy buttons will not appear until they are set.'
    );
}

/**
 * Check whether custom Permalinks are disabled.
 *
 * @return bool
 */
function simplerwc_are_permalinks_disabled()
{
    return get_option('permalink_structure') === "";
}

/**
 * Add hook for admin notice caused by disabled custom Permalinks
 */
function simplerwc_admin_notice_permalinks_are_disabled()
{
    add_action(
        'admin_notices',
        'simplerwc_display_admin_notice_for_disabled_permalinks'
    );
}

/**
 * Display the error message when custom Permalinks are disabled.
 */
function simplerwc_display_admin_notice_for_disabled_permalinks()
{
    printf(
        '<div class="notice notice-error"><p>%s</p></div>',
        sprintf(
            'Simpler Plugin requires pretty <a href="https://wordpress.org/support/article/settings-permalinks-screen/" target="_blank">%s</a> to be enabled.',
            'Permalinks'
        )
    );
}
