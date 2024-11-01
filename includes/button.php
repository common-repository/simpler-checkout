<?php

const SIMPLERWC_BTNPOS_PRODUCT_PAGE = "simpler-product-page";
const SIMPLERWC_BTNPOS_PRODUCT_SHORTCODE = "simpler-product-custom";
const SIMPLERWC_BTNPOS_CART_SHORTCODE = "simpler-cart-custom";
const SIMPLERWC_BTNPOS_BEFORE_CHECKOUT = "simpler-before-checkout-form";
const SIMPLERWC_BTNPOS_PROCEED_TO_CHECKOUT = "simpler-proceed-to-checkout";
const SIMPLERWC_BTNPOS_MINICART = "simpler-mini-cart";

add_action('init', 'simplerwc_load_visual_hook_filters');
add_action('init', 'simplerwc_load_custom_shortcodes');

function simplerwc_load_custom_shortcodes()
{
    add_shortcode('simpler-product-checkout', 'simplerwc_product_checkout_shortcode');
    add_shortcode('simpler-cart-checkout', 'simplerwc_cart_checkout_shortcode');
}

function simplerwc_load_visual_hook_filters()
{
    add_filter(
        get_option('simplerwc_product_button_placement', 'woocommerce_after_add_to_cart_quantity'),
        'simplerwc_add_checkout_after_main_content',
        10
    );

    add_filter(
        get_option('simplerwc_minicart_button_placement', 'woocommerce_widget_shopping_cart_before_buttons'),
        'simplerwc_render_mini_cart',
        10
    );

    add_filter(
        get_option('simplerwc_cart_page_button_placement'),
        'simplerwc_render_proceed_to_checkout',
        10
    );

    add_filter(
        get_option('simplerwc_checkout_page_button_placement'),
        'simplerwc_render_before_checkout_form',
        10
    );
}

function simplerwc_product_checkout_shortcode($atts)
{
    if (!simplerwc_should_render_button()) {
        return;
    }

    $a = shortcode_atts(['product_id' => NULL], $atts);
    if (!isset($atts['product_id'])) {
        global $product;
        if (!$product) {
            return '';
        }
    } else {
        $product = \wc_get_product($a['product_id']);
    }

    if (!($product instanceof WC_Product)) {
        return '';
    }

    if (apply_filters('simplerwc_should_render_product_button', true, $product)) {
        return simplerwc_button("shortcode_product", simplerwc_prepare_product($product), SIMPLERWC_BTNPOS_PRODUCT_SHORTCODE);
    }
}

function simplerwc_cart_checkout_shortcode($atts)
{
    if (!simplerwc_should_render_button()) {
        return '';
    }

    $cart = WC()->cart;
    if (is_null($cart) || !($cart instanceof WC_Cart)) {
        return '';
    }

    if (apply_filters('simplerwc_should_render_cart_button', true, $cart)) {
        return simplerwc_button("shortcode_cart", simplerwc_prepare_cart($cart), SIMPLERWC_BTNPOS_CART_SHORTCODE);
    }
}

function simplerwc_add_checkout_after_main_content()
{
    if (!simplerwc_should_render_button()) {
        return;
    }

    if (!get_option('simpler_auto_render_product_button')) {
        return;
    }

    global $product;
    if (apply_filters('simplerwc_should_render_product_button', true, $product)) {
        echo simplerwc_button(get_option('simplerwc_product_button_placement'), simplerwc_prepare_product($product), SIMPLERWC_BTNPOS_PRODUCT_PAGE);
    };
}

function simplerwc_render_mini_cart()
{
    if (!simplerwc_should_render_button()) {
        return;
    }

    if (!get_option('simplerwc_auto_render_minicart_button')) {
        return;
    }

    $cart          = WC()->cart;
    if (is_null($cart) || !($cart instanceof WC_Cart)) {
        return;
    }

    if (apply_filters('simplerwc_should_render_cart_button', true, $cart)) {
        echo simplerwc_button("minicart", simplerwc_prepare_cart($cart), SIMPLERWC_BTNPOS_MINICART);
    }
}

function simplerwc_render_proceed_to_checkout()
{
    if (!simplerwc_should_render_button()) {
        return;
    }

    if (!get_option('simpler_auto_render_cart_button')) {
        return;
    }

    $cart          = WC()->cart;
    if (is_null($cart) || !($cart instanceof WC_Cart)) {
        return;
    }

    if (apply_filters('simplerwc_should_render_cart_button', true, $cart)) {
        echo simplerwc_button(get_option('simplerwc_cart_page_button_placement'), simplerwc_prepare_cart($cart), SIMPLERWC_BTNPOS_PROCEED_TO_CHECKOUT);
    }
}

function simplerwc_render_before_checkout_form()
{
    if (!simplerwc_should_render_button()) {
        return;
    }

    if (!get_option('simplerwc_auto_render_checkout_page_button')) {
        return;
    }

    $cart          = WC()->cart;
    if (is_null($cart) || !($cart instanceof WC_Cart)) {
        return;
    }

    if (apply_filters('simplerwc_should_render_cart_button', true, $cart)) {
        echo simplerwc_button(get_option('simplerwc_checkout_page_button_placement'), simplerwc_prepare_cart($cart), SIMPLERWC_BTNPOS_BEFORE_CHECKOUT);
    }
}

function simplerwc_button($hook, $payload, $position)
{
    $html = simplerwc_button_style($position);
    $html .= '<simpler-checkout appId="' . get_option("simpler_api_key") . '" ';
    $html .= 'position="' . $hook . '" ';
    $html .= 'locale="' . get_locale() . '" ';
    $html .= 'payload="' . simplerwc_button_payload($payload) . '" ';
    $html .= simplerwc_maybe_render_accepted_cards();
    $html .= '></simpler-checkout>';
    return $html;
}

function simplerwc_maybe_render_accepted_cards()
{
    if (get_option('simplerwc_show_cards_notice')) {
        return 'withAcceptedCards="true" ';
    }
    return '';
}

function simplerwc_button_style($position)
{
    switch ($position) {
        case SIMPLERWC_BTNPOS_PRODUCT_PAGE:
            $style = get_option("simpler_product_button_custom_style");
            break;
        case SIMPLERWC_BTNPOS_MINICART:
            $style = get_option("simpler_mini_cart_button_custom_style");
            break;
        case SIMPLERWC_BTNPOS_PROCEED_TO_CHECKOUT:
            $style = get_option("simpler_cart_page_button_custom_style");
            break;
        case SIMPLERWC_BTNPOS_BEFORE_CHECKOUT:
            $style = get_option("simpler_checkout_page_button_custom_style");
            break;
        default:
            return false;
    }
    return '<style type="text/css">' . esc_html($style) . '</style>';
}

function simplerwc_button_payload($cart)
{
    return esc_html(base64_encode(json_encode([
        'cart' => $cart['items'],
        'coupon' => $cart['coupon'] ?? NULL,
        'locale' => get_locale(),
        'currency' => get_woocommerce_currency(),
    ])));
}

function simplerwc_prepare_product($product)
{
    return [
        'items' => [simplerwc_get_product_attributes($product)]
    ];
}

function simplerwc_prepare_cart($cart)
{
    $coupons = $cart->get_applied_coupons();
    $ret = [
        'items' => []
    ];

    if (is_array($coupons) && count($coupons) > 0 && is_string($coupons[0]) && strlen($coupons[0]) > 0) {
        $ret['coupon'] = $coupons[0];
    }

    foreach ($cart->get_cart_contents() as $cart_item) {
        if (!array_key_exists('data', $cart_item) || !($cart_item['data'] instanceof WC_Product)) {
            continue;
        }

        if (apply_filters('simplerwc_button_should_ignore_cart_item', false, $cart_item)) {
            continue;
        }

        // we'll bundle this in its container
        if (simplerwc_cart_item_is_bundled($cart_item)) {
            continue;
        }

        if (function_exists('wc_pb_is_bundle_container_cart_item') && \wc_pb_is_bundle_container_cart_item($cart_item)) {
            $bundled_items = \wc_pb_get_bundled_cart_items($cart_item);
            foreach ($bundled_items as $idx => $item) {
                $bundled_items[$idx]['quantity'] = $item['quantity'] / $cart_item['quantity'];
            }
            array_push($ret['items'], simplerwc_get_cart_item_attributes($cart_item, $bundled_items));
        } else {
            $bundled_items = [];
            $cross_sell_items = $cart_item["bundle_sells"] ?? [];
            foreach ($cross_sell_items as $id) {
                $bundled_items[] = $cart->get_cart_item($id);
            }
            array_push($ret['items'], simplerwc_get_cart_item_attributes($cart_item, $bundled_items));
        }
    }
    return $ret;
}

function simplerwc_get_cart_item_attributes($cart_item, $bundled = null)
{
    $attrs = simplerwc_get_product_attributes($cart_item['data']);

    if (isset($cart_item['quantity'])) {
        $attrs['quantity'] = $cart_item['quantity'];
    }

    if (is_a($cart_item['data'], 'WC_Product_Variation')) {
        /*** @var \WC_Product_Variation $product */
        $attrs['attributes'] = array_key_exists('variation', $cart_item) ? $cart_item['variation'] : $product->get_variation_attributes();
    }

    if (isset($bundled) && count($bundled) > 0) {
        $attrs['bundled'] = array_values(array_map(function ($el) {
            return simplerwc_get_cart_item_attributes($el);
        }, $bundled));
    }

    return $attrs;
}

/**
 * @param \WC_Product $product The product to extract information from
 */
function simplerwc_get_product_attributes($product)
{
    $attrs = [
        'product_id'         => strval($product->get_id()),
        'product_type'       => strval($product->get_type()),
        'in_stock'           => $product->is_in_stock(),
        'backorders_allowed' => $product->backorders_allowed(),
        'stock_quantity'     => $product->get_stock_quantity(),
        'sold_individually'  => $product->is_sold_individually(),
        'purchasable'        => $product->is_purchasable(),
        'attributes'         => apply_filters('simplerwc_button_get_product_attributes', [], $product)
    ];
    if (is_a($product, 'WC_Product_Variable')) {
        /** @var \WC_Product_Variable $product */
        $attrs['variations']   = array_map(function ($el) {
            return simplerwc_get_product_attributes(\wc_get_product($el['variation_id']));
        }, $product->get_available_variations());
    } else if (is_a($product, 'WC_Product_Variation')) {
        /** @var \WC_Product_Variation $product */
        $attrs['attributes'] = apply_filters('simplerwc_button_get_product_attributes', $product->get_variation_attributes(), $product);
    } else if (is_a($product, 'WC_Product_Bundle')) {
        /** @var \WC_Product_Bundle $product */
        $bundled = \WC_PB_DB::query_bundled_items([
            'return' => 'id=>product_id',
            'bundle_id' => [$product->get_id()]
        ]);
        $attrs['bundle_configuration'] = array_reduce($bundled, function ($acc, $el) {
            $product = \wc_get_product($el);
            if (!$product || \is_wp_error($product)) {
                return $acc;
            }
            $acc[strval($el)] = simplerwc_get_product_attributes($product);
            return $acc;
        }, []);
    }

    return $attrs;
}

function simplerwc_should_render_button()
{
    if (!simplerwc_check_simpler_configured()) {
        error_log('MISSING SIMPLER API KEY & SECRET');
        return false;
    }
    if (get_option('simpler_checkout_test_mode', true)) {
        return in_array('administrator', wp_get_current_user()->roles, true);
    }

    if (!is_array($excludedRoles = get_option('simplerwc_excluded_user_roles', [])) || !is_array($roles = wp_get_current_user()->roles)) {
        return true;
    }

    return count(array_intersect($excludedRoles, $roles)) === 0;
}

function simplerwc_cart_item_is_bundled($item)
{
    // check if is bundle product
    if (function_exists('wc_pb_is_bundled_cart_item') && \wc_pb_is_bundled_cart_item($item)) {
        return true;
    }

    if (array_key_exists('bundle_sell_of', $item)) {
        return true;
    }

    return false;
}
