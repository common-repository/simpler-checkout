<?php

namespace Simpler\Http\Controllers;

use Simpler\Http\Schemas\OrderSchema;
use Simpler\Models\{Address, InvoiceDetails, Order, OrderRequest, User};
use Simpler\Services\{OrderService, UserService};
use WP_REST_Request;
use WP_REST_Response;


class OrderController extends Controller
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
        $validation = \rest_validate_value_from_schema($body = $request->get_json_params(), OrderSchema::SCHEMA);
        if (\is_wp_error($validation)) {
            return new WP_REST_Response(json_encode($validation), 422);
        }

        $order_request = new OrderRequest(
            User::from_json($body['user']),
            Order::from_json($body['order']),
            isset($body['order']['shipto']) ? Address::from_json($body['order']['shipto']) : null,
            isset($body['invoice']) ? InvoiceDetails::from_json($body['invoice']) : null
        );

        $user_id = $this->userService->get_or_create($order_request->get_user());
        try {
            $order = $this->orderService->create_order($user_id, $order_request);
        } catch (\Exception $e) {
            return new WP_REST_Response([
                'error' => 'could not store order due to exception',
                'code' => 'ORDCRE001',
                'detail' => $e->getMessage()
            ], 500);
        }
        if (\is_wp_error($order)) {
            /** @var \WP_Error $order */
            return new WP_REST_Response([
                'error' => 'could not store order due to wp error',
                'code' => 'ORDCRE002',
                'detail' => $order->get_error_codes()
            ], 500);
        }

        $response = [
            'user_id'  => strval($user_id),
            'order_id' => strval(apply_filters('simplerwc_order_id', $order->get_id(), $order))
        ];

        return new WP_REST_Response($response, 201);
    }

    public function get_permission_callback()
    {
        return $this->WCBasicAuth();
    }
}
