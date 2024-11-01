<?php

namespace Simpler\Http\Controllers;

use Simpler\Exceptions\BaseException;
use Simpler\Http\Payloads\QuotationResponse;
use Simpler\Http\Schemas\QuotationSchema;
use Simpler\Models\{Address, CartItem, Quotation, QuotationRequest};
use Simpler\Services\QuotationService;
use WP_REST_Request;
use WP_REST_Response;

class QuotationController extends Controller
{
    protected $namespace = 'wc/simpler/v2';
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
     * @var QuotationService
     */
    protected $quotationService;
    /**
     * @var WP_REST_Request
     */
    private $request;

    public function __construct(QuotationService $quotationService)
    {
        parent::__construct();
        $this->quotationService = $quotationService;
    }

    /**
     * @param  WP_REST_Request  $request
     */
    public function handle($request): WP_REST_Response
    {
        $this->request = $request;
        $validation    = \rest_validate_value_from_schema($this->request->get_json_params(), QuotationSchema::SCHEMA);
        if (\is_wp_error($validation)) {
            return new WP_REST_Response(json_encode($validation), 422);
        }

        try {
            $quotations = $this->quote();
        } catch (\Exception $e) {
            return new WP_REST_Response(
                [
                    'code'    => $e instanceof BaseException ? $e->get_error_code() : $e->getCode(),
                    'message' => 'Failed to quote cart',
                    'error'   => $e->getMessage(),
                ],
                400
            );
        }

        return new WP_REST_Response((new QuotationResponse($quotations))->to_array());
    }

    /**
     * Quote incoming request
     *
     * @return Quotation[]
     * @throws \Exception
     */
    private function quote(): array
    {
        $items = [];
        $body  = $this->request->get_json_params();
        foreach ($body['items'] as $item) {
            $items[] = CartItem::from_json($item);
        }

        $request = new QuotationRequest($items);
        $request->set_coupon_code($body['coupon'] ?? '')
            ->set_user_email($body['email'] ?? '')
            ->set_shipping_address(isset($body['shipto']) ? Address::from_quotation_json($body['shipto']) : null);
        return $this->quotationService->quote($request);
    }

    public function get_permission_callback()
    {
        return $this->WCBasicAuth();
    }
}
