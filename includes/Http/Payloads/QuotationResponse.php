<?php

namespace Simpler\Http\Payloads;

use Simpler\Models\Money;
use Simpler\Models\Quotation;

class QuotationResponse
{
    private $response = [];
    /**
     * @var Quotation[]
     */
    private $quotations;

    public function __construct(array $quotations)
    {
        $this->quotations = $quotations;
        $this->build_response();
    }

    private function build_response()
    {
        $this->response['quotes'] = [];
        foreach ($this->quotations as $quotation) {
            $response = [
                'products'       => $this->get_products_response($quotation),
                'shipping'       => $quotation->get_shipping_rate() ? $this->get_shipping_rate_response($quotation)
                    : null,
                'discount_cents' => $quotation->get_discount_cents(),
                'total_cents'    => $quotation->get_total_cents(),
                'fees'           => $this->get_fees_response($quotation),
            ];

            if ($paymentMethods = $this->get_payment_methods_response($quotation)){
                $response['payment_methods'] = $paymentMethods;
            }

            $this->response['quotes'][] = $response;
        }
    }

    /**
     * Get JSON representation of shipping cost for given quotation.
     *
     * @param  Quotation  $quotation
     *
     * @return array
     */
    private function get_shipping_rate_response(Quotation $quotation): array
    {
        return [
            'id'          => $quotation->get_shipping_rate()->get_id(),
            'method_id'   => $quotation->get_shipping_rate()->get_method_id(),
            'label'       => wc_clean($quotation->get_shipping_rate()->get_label()),
            'cost_cents'  => $quotation->get_shipping_cents(),
            'tax_cents'   => $quotation->get_shipping_tax_cents(),
            'instance_id' => $quotation->get_shipping_rate()->get_instance_id(),
        ];
    }

    /**
     * Get JSON representation of products cost for given quotation.
     *
     * @param  Quotation  $quotation
     *
     * @return array
     */
    private function get_products_response(Quotation $quotation): array
    {
        $response = [];

        foreach ($quotation->get_products() as $product) {
            $data = [
                'id'                 => (string)$product->get_product_id(),
                'quantity'           => $product->get_quantity(),
                'subtotal_net_cents' => Money::to_cents($product->get_subtotal_net_cost()),
                'subtotal_tax_cents' => Money::to_cents($product->get_subtotal_tax_cost()),
                'subtotal_cents'     => Money::to_cents($product->get_subtotal_cost()),
                'cost_net_cents'     => Money::to_cents($product->get_total_net_cost()),
                'cost_tax_cents'     => Money::to_cents($product->get_total_tax_cost()),
                'cost_cents'         => Money::to_cents($product->get_total_cost()),
            ];

            if ( ! empty($product->get_attributes())) {
                foreach ($product->get_attributes() as $attribute) {
                    $data['attributes'][] = ['key' => $attribute->get_key(), 'value' => $attribute->get_value()];
                }
            }

            $response[] = $data;
        }

        return $response;
    }

    /**
     * Get JSON representation of fees for given quotation.
     *
     * @param  Quotation  $quotation
     *
     * @return array
     */
    private function get_fees_response(Quotation $quotation): array
    {
        $response = [];

        foreach ($quotation->get_fees() as $fee) {
            $response[] = [
                'id'         => $fee->get_id(),
                'title'      => $fee->get_name(),
                'cost_cents' => Money::to_cents($fee->get_total() + $fee->get_tax()),
            ];
        }

        return $response;
    }

    public function to_array(): array
    {
        return $this->response;
    }

    private function get_payment_methods_response(Quotation $quotation)
    {
        $paymentMethods = [];

        foreach ($quotation->get_payment_methods() as $paymentMethod) {
            $paymentMethods[] = [
                "id"          => $paymentMethod->getId(),
                "type"        => $paymentMethod->getType(),
                "name"        => $paymentMethod->getName(),
                "net_cents"   => $paymentMethod->getNetCents(),
                "tax_cents"   => $paymentMethod->getTaxCents(),
                "total_cents" => $paymentMethod->getTotalCents(),
            ];
        }

        return $paymentMethods;
    }
}
