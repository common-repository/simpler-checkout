<?php

/**
 * Plugin Name: Simpler Checkout
 * Plugin URI: https://simpler.so/
 * Author: Simpler
 * Author URI: https://simpler.so
 * Description: Simpler Checkout lets your customers complete their purchases in seconds, with any payment method they want, in any device or browser and without a password.
 * Tags: woocommerce, checkout, payments, conversion rate
 * Version: 1.0.3
 * Requires at least: 5.1
 * Tested up to: 6.3.1
 * Requires PHP: 7.0
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-3.0.html
 *
 * @package Simpler
 */

defined('ABSPATH') || exit;

define('SIMPLERWC_PATH', plugin_dir_path(__FILE__));
define('SIMPLERWC_URL', plugin_dir_url(__FILE__));
define('SIMPLERWC_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('SIMPLERWC_PAYMENT_METHOD_SLUG', 'Simpler');

require_once SIMPLERWC_PATH . 'includes/woocommerce.php';

if (simplerwc_is_woocommerce_active()) {
    if (simplerwc_are_permalinks_disabled()) {
        simplerwc_admin_notice_permalinks_are_disabled();
    }

    if (!simplerwc_check_simpler_configured()) {
        simplerwc_admin_notice_incomplete_configuration();
    }

    /*
	 * Composer provides a convenient, automatically generated class loader for
	 * our plugin. We'll require it here so that we don't have to worry about manual
	 * loading any of our classes later on.
	 */
    require_once SIMPLERWC_PATH . 'vendor/autoload.php';
    require_once SIMPLERWC_PATH . 'includes/admin.php';
    require_once SIMPLERWC_PATH . 'includes/constants.php';
    require_once SIMPLERWC_PATH . 'includes/assets.php';
    require_once SIMPLERWC_PATH . 'includes/hooks.php';
    require_once SIMPLERWC_PATH . 'includes/button.php';
    require_once SIMPLERWC_PATH . 'includes/routes.php';
    require_once SIMPLERWC_PATH . 'includes/gateway.php';
    require_once SIMPLERWC_PATH . 'includes/compat.php';
} else {
    simplerwc_admin_notice_woocommerce_is_missing();
}
