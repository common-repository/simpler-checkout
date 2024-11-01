<?php

namespace Simpler\Services;

use Exception;
use Simpler\Exceptions\{BaseException, InvalidProductException};
use Simpler\Models\CartItem;

trait CartHelper
{

    private function initialize_cart()
    {
        // Disable updating cart, in metadata settings (wp_usermeta), of currently authenticated user.
        // This prevents updating an existing cart with the one given in the quotation request.
        add_filter('woocommerce_persistent_cart_enabled', '__return_false');
        WC()->cart     = new \WC_Cart();
        WC()->session  = new \WC_Session_Handler();
        WC()->cart->get_cart_from_session();
    }

    /**
     * @throws BaseException|Exception
     */
    private function add_item_to_cart(CartItem $item)
    {
        $product = \wc_get_product($item->get_product_id());
        $productAdded = false;
        if (is_a($product, 'WC_Product_Bundle')) {
            $productAdded = $this->add_bundle_to_cart($product, $item);
        } else {
            $productAdded = \WC()->cart->add_to_cart(
                $item->get_product_id(),
                $item->get_quantity(),
                NULL,
                $item->get_attributes_array(),
                apply_filters('simplerwc_get_cart_item_data', [], $item)
            );

            // Woo Product Bundles configured through Linked Products
            foreach ($item->get_bundle_configuration() as $bundled) {
                \WC()->cart->add_to_cart(
                    $bundled['product_id'],
                    $bundled['quantity'],
                    NULL,
                    $bundled['attributes'],
                    apply_filters('simplerwc_get_cart_item_data', ['bundle_sell_of' => $productAdded], $bundled)
                );
            }
            if (class_exists('\WC_PB_BS_Cart') && method_exists('\WC_PB_BS_Cart', 'load_bundle_sells_into_session')) {
                \WC_PB_BS_Cart::load_bundle_sells_into_session(\WC()->cart);
            }
        }

        if (is_bool($productAdded) && !$productAdded) {
            throw $this->addProductToCartException
                ?: new InvalidProductException(json_encode(WC()->session->get('wc_notices')));
        }

        do_action('simplerwc_after_add_to_cart', $productAdded, $item);
    }

    private function add_bundle_to_cart($bundle, $item)
    {
        // the final bundle configuration to be added to cart
        $configuration = [];

        // get bundle configurations
        $bundled = \WC_PB_DB::query_bundled_items(array(
            'return'    => 'id=>product_id',
            'bundle_id' => array($bundle->get_id())
        ));

        // try to match the requested configuration with one of the registered bundles
        foreach ($item->get_bundle_configuration() as $bundle_config) {
            $found = false;
            foreach ($bundled as $bundle_id => $product_id) {
                if ($bundle_config['product_id'] == $product_id) {
                    $configuration[$bundle_id] = $bundle_config;
                    $configuration[$bundle_id]['optional_selected'] = 'yes';
                    $found = true;
                    break;
                }
            }

            // if not found, check if variation
            if (!$found) {
                $product = \wc_get_product($bundle_config['product_id']);
                if ($product->is_type('variation')) {
                    // if it's a variation try searching again with parent's id
                    /** @var \WC_Product_Variable $product */
                    foreach ($bundled as $bundle_id => $product_id) {
                        if ($product->get_parent_id() == $product_id) {
                            $configuration[$bundle_id] = $this->construct_variation_configuration($bundle_config, $product);
                            break;
                        }
                    }
                }
            }
        }

        // fill configuration keys for not-selected bundled items
        foreach ($bundled as $bundle_id => $product_id) {
            if (!array_key_exists($bundle_id, $configuration)) {
                $configuration[$bundle_id] = [
                    'optional_selected' => 'no'
                ];
            }
        }

        return \WC_PB()->cart->add_bundle_to_cart(
            $item->get_product_id(),
            $item->get_quantity(),
            $configuration
        );
    }

    private function construct_variation_configuration($bundle, $product)
    {
        $c = $bundle;
        foreach ($product->get_variation_attributes() as $k => $v) {
            if (!array_key_exists($k, $bundle['attributes'])) {
                $c['attributes'][$k] = $v;
            }
        }
        $c['product_id'] = $product->get_parent_id();
        $c['variation_id'] = $bundle['product_id'];
        $c['optional_selected'] = 'yes';
        return $c;
    }
}
