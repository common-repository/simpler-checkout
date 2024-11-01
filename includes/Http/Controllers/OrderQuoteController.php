<?php

namespace Simpler\Http\Controllers;

use Simpler\Exceptions\InvalidCouponException;
use Simpler\Http\Schemas\OrderQuoteSchema;
use WP_REST_Request;
use WP_REST_Response;

class OrderQuoteController extends Controller
{
    protected $namespace = 'wc/simpler/v1';
    /**
     * Route name.
     *
     * @var string
     */
    protected $route = 'orders/quote';

    /**
     * Route methods.
     *
     * @var string
     */
    protected $method = 'POST';

    /**
     * @param  WP_REST_Request  $request
     *
     * @return WP_REST_Response
     */
    public function handle($request)
    {
        $validation = \rest_validate_value_from_schema($body = $request->get_json_params(), OrderQuoteSchema::$schema);

        if (\is_wp_error($validation)) {
            return new WP_REST_Response(json_encode($validation), 422);
        }

        $response = [];
        try {
            $discountCents = $this->get_coupon_discount_cents($body['items'], $body['coupon'] ?? null);
        } catch (\Exception $e) {
            $response['message'] = 'Failed to produce quote for cart and coupon combination';
            $response['error']   = $e->getMessage();

            return new WP_REST_Response($response, 400);
        }

        $response['discount_cents'] = $discountCents;
        return new WP_REST_Response($response);
    }

    /**
     * @return int
     * @throws \Exception
     */
    private function get_coupon_discount_cents(array $products, string $couponCode = null)
    {
        if (is_null($couponCode)) {
            return 0;
        }

        if (wc_get_coupon_id_by_code($couponCode) <= 0) {
            throw new InvalidCouponException('Supplied coupon does not exist');
        }

        $cart = new \WC_Cart();
        add_action('shutdown', function () {
            WC()->session->destroy_session();
        });

        foreach ($products as $product) {
            $cart->add_to_cart($product['product_id'], $product['quantity']);
        }

        $couponCode = mb_strtolower($couponCode);
        $cart->apply_coupon($couponCode);
        return self::to_cents($cart->get_coupon_discount_amount($couponCode, !wc_prices_include_tax()));
    }

    private static function to_cents($amount): int
    {
        return wc_cart_round_discount($amount, 2) * 100;
    }

    public function get_permission_callback()
    {
        return $this->WCBasicAuth();
    }
}
