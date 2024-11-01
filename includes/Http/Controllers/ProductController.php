<?php

namespace Simpler\Http\Controllers;

use Simpler\Exceptions\BaseException;
use Simpler\Exceptions\InvalidProductException;
use WP_REST_Request;
use WP_REST_Response;

class ProductController extends Controller
{
    protected $namespace = 'wc/simpler/v1';
    /**
     * Route name.
     *
     * @var string
     */
    protected $route = 'product';

    /**
     * Route methods.
     *
     * @var string
     */
    protected $method = 'GET';

    /**
     * @var WP_REST_Request
     */
    private $request;


    /**
     * @var WC_REST_Products_Controller
     */
    private $wc_rest_products_controller;

    public function __construct()
    {
        parent::__construct();
        $this->wc_rest_products_controller = new \WC_REST_Products_Controller();
    }

    /**
     * @param WP_REST_Request $request Full details about the request.
     * @return array|WP_Error|WP_REST_Response
     */
    public function handle($request): WP_REST_Response
    {
        $this->request = $request;
        $params = $this->request->get_query_params();
        if (!array_key_exists('product_ids', $params)) {
            return new WP_REST_Response(
                [
                    'code'    => '400',
                    'message' => 'Bad Request',
                    'error'   => 'Bad Request'
                ],
                400
            );
        }

        $ids = $params['product_ids'];

        if (!is_array($ids) || (sizeof($ids) == 0)) {
            return new WP_REST_Response(
                [
                    'code'    => '400',
                    'message' => 'Bad Request',
                    'error'   => 'Bad Request'
                ],
                400
            );
        }

        try {
            $products = $this->get_product_objects($ids);
        } catch (\Exception $e) {
            return new WP_REST_Response(
                [
                    'code'    => $e instanceof BaseException ? $e->get_error_code() : $e->getCode(),
                    'message' => 'Failed to retrieve products',
                    'error'   => $e->getMessage(),
                ],
                400
            );
        }

        if (\is_wp_error($products)) {
            return new WP_REST_Response(
                [
                    'code'    => $products->get_error_code(),
                    'message' => $products->get_error_message(),
                    'error'   => $products->get_error_data()
                ],
                500
            );
        }

        if (empty($products)) {
            return new \WP_REST_Response([], 404);
        }

        $res = $this->products_to_response($products);

        return $res;
    }

    /**
     * Retrieve all requested products (including variations & bundled)
     * @param  array $ids Array of ids.
     * @throws BaseException|Exception
     * @return array|WP_Error
     */
    public function get_product_objects($ids)
    {
        $products = array();
        $extra_ids = array();
        foreach ($ids as $id) {
            $prod = \wc_get_product($id);
            if ($prod === false) {
                throw new InvalidProductException();
            }
            if (\is_wp_error($prod)) {
                return $prod;
            }

            if ($prod->is_type('variation')) {
                $extra_ids[$prod->get_parent_id()] = '';
            }

            $products[] = $prod;
        }

        foreach ($products as $prod) {
            unset($extra_ids[$prod->get_id()]);
        }

        $extra_ids = apply_filters('simplerwc_products_extra_ids', $extra_ids);
        foreach ($extra_ids as $id => $v) {
            $prod = \wc_get_product($id);
            if (\is_wp_error($prod)) {
                return $prod;
            }

            $products[] = $prod;
        }

        return $products;
    }

    /**
     * Prepare and return all product objects as rest response.
     * @param  array         $object  Object data.
     * @return WP_REST_Response
     */
    public function products_to_response($products)
    {
        $data = array();

        foreach ($products as $product) {
            $resp = $this->wc_rest_products_controller->prepare_object_for_response($product, $this->request);

            $obj = $resp->data;
            if ($product->is_type('variable')) {
                add_filter('woocommerce_available_variation', [$this, 'add_extra_data_to_variation'], 10, 3);
                $obj['variations_data'] = $product->get_available_variations('array');
                remove_filter('woocommerce_available_variation', [$this, 'add_extra_data_to_variation'], 10, 3);

                $obj['attributes_data'] = $this->attributes($product);
            }
            $data[] = $obj;
        }

        return new \WP_REST_Response($data, 200);
    }

    public function add_extra_data_to_variation($array, $variable, $variation)
    {
        $array['permalink'] = $variation->get_permalink();
        $array['name'] = $variation->get_name();
        $array['type'] = $variation->get_type();
        $array['description'] = $variation->get_description();
        $array['shipping_required'] = $variation->needs_shipping();
        return $array;
    }

    /**
     * @param  WC_Product $product
     * @return array
     */
    public function attributes($product)
    {
        if (!$product->is_type('variable')) {
            return [];
        }

        $attributes = array();

        foreach ($product->get_attributes() as $key => $attribute) {
            $attribute_key = $this->attribute_key($key);

            $attributes[$attribute_key] = $attribute->get_data();
            $attributes[$attribute_key]['label'] = \wc_attribute_label($attribute->get_name(), $product);
            $attributes[$attribute_key]['options'] = $this->attribute_options($attribute);
        }

        return $attributes;
    }

    /**
     * @param  WC_Product_Attribute $product
     * @return array
     */
    public function attribute_options($attribute)
    {
        $options = array();

        $terms = $attribute->get_terms();
        if ($terms != null) {
            foreach ($terms as $term) {
                $options[] = [
                    'id'   => $term->term_id,
                    'name' => $term->name,
                    'slug' => $term->slug
                ];
            }
            return $options;
        }

        $opts = $attribute->get_options();
        $slugs = $attribute->get_slugs();
        foreach ($opts as $i => $option) {
            $options[] = [
                'name' => $option,
                'slug' => $slugs[$i]
            ];
        }

        return $options;
    }

    /**
     * Formats the attribute key.
     * @param string $att_key Non-normalized key.
     * @return string
     */
    protected function attribute_key($att_key)
    {
        return 'attribute_' . sanitize_title($att_key);
    }

    public function get_permission_callback()
    {
        return $this->WCBasicAuth();
    }
}
