<?php

namespace Simpler\Models;

class Fee
{
    /**
     * A kebab-case representation of $name.
     *
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $name;
    /**
     * The original fee amount regardless of cart's content.
     *
     * @var string
     */
    private $amount;
    /**
     * The adjusted fee amount based on cart's content.
     * For example, if the original fee $amount is "-100" and the cart contains a product worth 50,
     * $total will equal -50.0 to prevent negative total cart values.
     *
     * @var float
     */
    private $total;
    /**
     * The adjusted tax based on $total.
     *
     * @var float
     */
    private $tax;

    public function __construct($id, $name, $amount, $total, $tax)
    {
        $this->id     = $id;
        $this->name   = $name;
        $this->amount = $amount;
        $this->total  = $total;
        $this->tax    = $tax;
    }

    /**
     * @return string
     */
    public function get_id(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function get_name(): string
    {
        return $this->name;
    }

    /**
     * @return float
     */
    public function get_total(): float
    {
        return $this->total;
    }

    /**
     * @return float
     */
    public function get_tax(): float
    {
        return $this->tax;
    }
}
