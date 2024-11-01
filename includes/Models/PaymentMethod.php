<?php

namespace Simpler\Models;

class PaymentMethod
{
    const COD = 'COD';
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $type;
    /**
     * @var float
     */
    private $netCents;
    /**
     * @var float
     */
    private $taxCents;
    /**
     * @var float
     */
    private $totalCents;

    public function __construct($id, $name, $type, $totalCents, $netCents, $taxCents)
    {
        $this->id     = $id;
        $this->name   = $name;
        $this->type = $type;
        $this->netCents = $netCents;
        $this->taxCents = $taxCents;
        $this->totalCents = $totalCents;
    }

    public static function from_json(array $json): self
    {
        return new PaymentMethod(
            $json['id'],
            $json['name'] ?? '',
            $json['type'],
            $json['total_cents'] ?? null,
            $json['net_cents'] ?? null,
            $json['tax_cents'] ?? null
        );
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getNetCents()
    {
        return $this->netCents;
    }

    public function getTaxCents()
    {
        return $this->taxCents;
    }

    public function getTotalCents()
    {
        return $this->totalCents;
    }
}
