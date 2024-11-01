<?php

const SIMPLERWC_PAYLOAD_PREFIX = "simplerwc-payload";

const SIMPLERWC_SEPARATOR_NONE = "simplerwc-separator-none";
const SIMPLERWC_SEPARATOR_TOP = "simplerwc-separator-top";
const SIMPLERWC_SEPARATOR_BOTTOM = "simplerwc-separator-bottom";

function simplerwc_legacy_load_shortcodes()
{
    add_shortcode('simpler-product-checkout', 'simplerwc_legacy_product_checkout_shortcode');
    add_shortcode('simpler-cart-checkout', 'simplerwc_legacy_cart_checkout_shortcode');
}

function simplerwc_legacy_load_visual_hooks()
{
    add_filter(
        get_option('simplerwc_product_button_placement', 'woocommerce_after_add_to_cart_quantity'),
        'simplerwc_legacy_add_checkout_after_main_content',
        10
    );

    add_filter(
        'woocommerce_widget_shopping_cart_before_buttons',
        'simplerwc_legacy_render_mini_cart',
        10
    );

    add_filter(
        get_option('simplerwc_cart_page_button_placement'),
        'simplerwc_legacy_render_proceed_to_checkout',
        10
    );

    add_filter(
        get_option('simplerwc_checkout_page_button_placement'),
        'simplerwc_legacy_render_before_checkout_form',
        10
    );
}

function simplerwc_legacy_product_checkout_shortcode($atts)
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

    $target = uniqid(SIMPLERWC_PAYLOAD_PREFIX);
    $content = simplerwc_simple_button($target, "Quick Buy", SIMPLERWC_BTNPOS_PRODUCT_SHORTCODE);
    $content .= simplerwc_cart_contents_tag($target, simplerwc_prepare_product($product));
    return $content;
}

function simplerwc_legacy_cart_checkout_shortcode($atts)
{
    if (!simplerwc_should_render_button()) {
        return '';
    }

    $cart = WC()->cart;
    if (is_null($cart) || !($cart instanceof WC_Cart)) {
        return '';
    }

    $target = uniqid(SIMPLERWC_PAYLOAD_PREFIX);
    $content = simplerwc_simple_button($target, "Quick Checkout", SIMPLERWC_BTNPOS_CART_SHORTCODE);
    $content .= simplerwc_cart_contents_tag($target, simplerwc_prepare_cart($cart));
    return $content;
}

function simplerwc_legacy_add_checkout_after_main_content()
{
    if (!simplerwc_should_render_button()) {
        return;
    }

    if (!get_option('simpler_auto_render_product_button')) {
        return;
    }

    $separatorPos = [
        'woocommerce_before_add_to_cart_form' => SIMPLERWC_SEPARATOR_BOTTOM,
        'woocommerce_before_add_to_cart_quantity' => SIMPLERWC_SEPARATOR_BOTTOM,
        'woocommerce_after_add_to_cart_quantity' => SIMPLERWC_SEPARATOR_BOTTOM,
        'woocommerce_after_add_to_cart_form' => SIMPLERWC_SEPARATOR_TOP,
        'woocommerce_after_add_to_cart_button' => SIMPLERWC_SEPARATOR_TOP,
        'woocommerce_after_single_product_summary' => SIMPLERWC_SEPARATOR_NONE
    ][get_option('simplerwc_product_button_placement')];

    global $product;
    $target = uniqid(SIMPLERWC_PAYLOAD_PREFIX);
    echo simplerwc_simple_button($target, "Quick Buy", SIMPLERWC_BTNPOS_PRODUCT_PAGE, $separatorPos);
    echo simplerwc_cart_contents_tag($target, simplerwc_prepare_product($product));
}

function simplerwc_legacy_render_mini_cart()
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

    $target = uniqid(SIMPLERWC_PAYLOAD_PREFIX);
    echo simplerwc_simple_button($target, "Quick Checkout", SIMPLERWC_BTNPOS_MINICART);
    echo simplerwc_cart_contents_tag($target, simplerwc_prepare_cart($cart));
}

function simplerwc_legacy_render_proceed_to_checkout()
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

    $target        = uniqid(SIMPLERWC_PAYLOAD_PREFIX);
    echo simplerwc_simple_button($target, "Quick Checkout", SIMPLERWC_BTNPOS_PROCEED_TO_CHECKOUT, SIMPLERWC_SEPARATOR_BOTTOM);
    echo simplerwc_cart_contents_tag($target, simplerwc_prepare_cart($cart));
}

function simplerwc_legacy_render_before_checkout_form()
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

    $target        = uniqid(SIMPLERWC_PAYLOAD_PREFIX);
    echo simplerwc_marquee_button($target, 'Quick Checkout', SIMPLERWC_BTNPOS_BEFORE_CHECKOUT);
    echo simplerwc_cart_contents_tag($target, simplerwc_prepare_cart($cart));
}

function simplerwc_simple_button($id, $text, $positionId, $separatorPos = SIMPLERWC_SEPARATOR_NONE)
{
    $html = simplerwc_button_style($positionId);
    $html .= '<div class="simpler-container ' . $positionId . '">';
    $html .= '<div class="simpler-wrap">';
    if ($separatorPos === SIMPLERWC_SEPARATOR_TOP) {
        $html .= simplerwc_button_separator_template();
    }

    $isSingleProduct = $positionId === SIMPLERWC_BTNPOS_PRODUCT_SHORTCODE || $positionId === SIMPLERWC_BTNPOS_PRODUCT_PAGE;
    $html .= simplerwc_button_template($id, $text, $isSingleProduct);

    if (simplerwc_should_render_cards_notice($positionId)) {
        $html .= simplerwc_accepted_cards_notice_template();
    }
    if ($separatorPos === SIMPLERWC_SEPARATOR_BOTTOM) {
        $html .= simplerwc_button_separator_template();
    }
    $html .= '</div></div>';
    return $html;
}

function simplerwc_marquee_button($id, $text, $positionId)
{
    $html = simplerwc_button_style($positionId);
    $html .= '<div class="simpler-container ' . $positionId . '">';
    $html .= '<div class="simpler-wrap simpler-marquee-wrap">';
    $html .= '<div class="simpler-marquee-top">Safer, Faster, Simpler</div>';
    $html .= simplerwc_button_template($id, $text, $positionId === SIMPLERWC_BTNPOS_PRODUCT_PAGE);
    if (simplerwc_should_render_cards_notice($positionId)) {
        $html .= simplerwc_accepted_cards_notice_template();
    }
    $html .= '<div class="simpler-marquee-bottom">OR</div>';
    $html .= '</div></div>';
    return $html;
}

function simplerwc_cart_contents_tag($id, $cart)
{
    $cartPayload = base64_encode(json_encode([
        'cart' => $cart,
        'locale' => get_locale(),
        'currency' => get_woocommerce_currency(),
    ]));
    return '<input id="' . $id . '" type="hidden" value="' . esc_html($cartPayload) . '" />';
}

function simplerwc_should_render_cards_notice($positionId)
{
    return get_option('simplerwc_show_cards_notice');
}
