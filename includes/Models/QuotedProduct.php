<?php

namespace Simpler\Models;

class QuotedProduct
{
    /**
     * @var int
     */
    private $productId;
    /**
     * @var int
     */
    private $quantity;
    /**
     * @var ProductAttribute[]
     */
    private $attributes = [];
    /**
     * @var float
     */
    private $totalNetCost;
    /**
     * @var float
     */
    private $totalTaxCost;
    /**
     * @var float
     */
    private $totalCost;
    /**
     * @var float
     */
    private $subtotalNetCost;
    /**
     * @var float
     */
    private $subtotalTaxCost;
    /**
     * @var float
     */
    private $subtotalCost;

    public function __construct(
        $productId,
        $quantity,
        $totalNetCost,
        $totalTaxCost,
        $totalCost,
        $subtotalNetCost,
        $subtotalTaxCost,
        $subtotalCost,
        array $attributes
    ) {
        $this->productId       = $productId;
        $this->quantity        = $quantity;
        $this->totalCost       = $totalCost;
        $this->subtotalCost    = $subtotalCost;
        $this->attributes      = $attributes;
        $this->totalNetCost    = $totalNetCost;
        $this->totalTaxCost    = $totalTaxCost;
        $this->subtotalNetCost = $subtotalNetCost;
        $this->subtotalTaxCost = $subtotalTaxCost;
    }

    /**
     * @return int
     */
    public function get_product_id(): int
    {
        return $this->productId;
    }

    /**
     * @return int
     */
    public function get_quantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return float
     */
    public function get_total_cost(): float
    {
        return $this->totalCost;
    }

    /**
     * @return float
     */
    public function get_subtotal_cost(): float
    {
        return $this->subtotalCost;
    }

    /**
     * @return ProductAttribute[]
     */
    public function get_attributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return float
     */
    public function get_total_net_cost(): float
    {
        return $this->totalNetCost;
    }

    /**
     * @return float
     */
    public function get_total_tax_cost(): float
    {
        return $this->totalTaxCost;
    }

    /**
     * @return float
     */
    public function get_subtotal_net_cost(): float
    {
        return $this->subtotalNetCost;
    }

    /**
     * @return float
     */
    public function get_subtotal_tax_cost(): float
    {
        return $this->subtotalTaxCost;
    }
}
