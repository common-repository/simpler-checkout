<?php

namespace Simpler\Http\Controllers;

use Simpler\Http\Schemas\OrderSchema;
use Simpler\Models\Address;
use Simpler\Models\Order;
use Simpler\Models\OrderRequest;
use Simpler\Models\User;
use Simpler\Services\OrderService;
use Simpler\Services\UserService;
use WP_REST_Request;
use WP_REST_Response;


class OrderCreateController extends Controller
{

    /**
     * Route name.
     *
     * @var string
     */
    protected $route = 'orders';

    /**
     * Route methods.
     *
     * @var string
     */
    protected $method = 'POST';
    /**
     * @var UserService
     */
    private $userService;
    /**
     * @var OrderService
     */
    private $orderService;

    public function __construct(UserService $userService = null, OrderService $orderService = null)
    {
        parent::__construct();
        $this->userService  = $userService ?: new  UserService();
        $this->orderService = $orderService ?: new OrderService();
    }

    /**
     * @param  WP_REST_Request  $request
     *
     * @return WP_REST_Response
     */
    public function handle($request)
    {
        if (!static::validate_crc($request, \get_option('simpler_api_secret'))) {
            return new WP_REST_Response([], 403);
        };

        $validation = \rest_validate_value_from_schema($body = $request->get_json_params(), OrderSchema::SCHEMA);
        if (\is_wp_error($validation)) {
            return new WP_REST_Response(json_encode($validation), 422);
        }

        $order_request = new OrderRequest(
            User::from_json($body['user']),
            Order::from_json($body['order']),
            isset($body['order']['shipto']) ? Address::from_json($body['order']['shipto']) : null
        );

        $user_id = $this->userService->get_or_create($order_request->get_user());
        try {
            $order = $this->orderService->create_order($user_id, $order_request);
        } catch (\Exception $e) {
            return new WP_REST_Response($this->order_creation_error_response(), 500);
        }
        if (\is_wp_error($order)) {
            return new WP_REST_Response($this->order_creation_error_response(), 500);
        }

        $response = [
            'user_id'  => strval($user_id),
            'order_id' => strval($order->get_id())
        ];

        return new WP_REST_Response($response, 201);
    }

    private function order_creation_error_response()
    {
        return [
            'error' => 'Could not store order',
            'code'  => 'ORDCRE001'
        ];
    }
}
