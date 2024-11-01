<?php

add_action('wp_enqueue_scripts', 'simplerwc_init_appid');
add_action('wp_enqueue_scripts', 'simplerwc_enqueue_assets');

function simplerwc_init_appid()
{
    $id = get_option('simpler_api_key');
    if ($id) {
        echo '<script type="text/javascript">window.simplerCheckoutAppId = "' . $id . '";</script>';
    }
}

function simplerwc_enqueue_assets()
{
    $uri = simplerwc_get_sdk_uri() . '?ts=' . time();
    wp_enqueue_script('simpler-checkout-sdk-script', $uri, [], SIMPLERWC_VERSION);
}
