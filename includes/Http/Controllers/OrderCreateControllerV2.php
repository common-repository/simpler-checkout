<?php

namespace Simpler\Http\Controllers;

use Simpler\Http\Schemas\OrderSchema;
use Simpler\Models\{Address, Order, OrderRequest, User};
use Simpler\Services\{OrderServiceV2, UserService};
use WP_REST_Request;
use WP_REST_Response;


class OrderCreateControllerV2 extends Controller
{

    protected $namespace = 'wc/simpler/v2';
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
     * @var OrderServiceV2
     */
    private $orderService;

    public function __construct(UserService $userService = null, OrderServiceV2 $orderService = null)
    {
        parent::__construct();
        $this->userService  = $userService ?: new  UserService();
        $this->orderService = $orderService ?: new OrderServiceV2();
    }

    /**
     * @param  WP_REST_Request  $request
     *
     * @return WP_REST_Response
     */
    public function handle($request)
    {
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

    public function get_permission_callback()
    {
        return $this->WCBasicAuth();
    }
}
