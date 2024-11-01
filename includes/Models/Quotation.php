<?php

namespace Simpler\Models;

use WC_Shipping_Rate;

class Quotation
{
    /**
     * @var int
     */
    private $discountCents = 0;
    /**
     * @var WC_Shipping_Rate
     */
    private $shippingRate;
    /**
     * @var int
     */
    private $shippingCents = 0;
    /**
     * @var int
     */
    private $shippingTaxCents = 0;
    /**
     * @var int
     */
    private $totalCents = 0;
    /**
     * @var QuotedProduct[]
     */
    private $products = [];
    /**
     * @var Fee[]
     */
    private $fees = [];
    /**
     * @var PaymentMethod[]
     */
    private $paymentMethods = [];

    /**
     * @param  int  $discountCents
     *
     * @return Quotation
     */
    public function set_discount_cents(int $discountCents): Quotation
    {
        $this->discountCents = $discountCents;

        return $this;
    }

    /**
     * @return int
     */
    public function get_discount_cents(): int
    {
        return $this->discountCents;
    }

    /**
     * @param  WC_Shipping_Rate  $shippingRate
     *
     * @return Quotation
     */
    public function set_shipping_rate(WC_Shipping_Rate $shippingRate): Quotation
    {
        $this->shippingRate = $shippingRate;

        return $this;
    }

    /**
     * @return WC_Shipping_Rate
     */
    public function get_shipping_rate()
    {
        return $this->shippingRate;
    }

    public function set_shipping_cents($cents): Quotation
    {
        $this->shippingCents = $cents;

        return $this;
    }

    public function get_shipping_cents(): int
    {
        return $this->shippingCents;
    }

    public function set_shipping_tax_cents($cents): Quotation
    {
        $this->shippingTaxCents = $cents;

        return $this;
    }

    public function get_shipping_tax_cents(): int
    {
        return $this->shippingTaxCents;
    }

    /**
     * @return int
     */
    public function get_total_cents(): int
    {
        return $this->totalCents;
    }

    /**
     * @param  int  $totalCents
     */
    public function set_total_cents(int $totalCents)
    {
        $this->totalCents = $totalCents;
    }

    /**
     * @return QuotedProduct[]
     */
    public function get_products(): array
    {
        return $this->products;
    }

    /**
     * @param  QuotedProduct[]  $products
     *
     * @return Quotation
     */
    public function set_products(array $products): Quotation
    {
        $this->products = $products;

        return $this;
    }

    /**
     * @param  Fee[]  $fees
     *
     * @return Quotation
     */
    public function set_fees(array $fees): Quotation
    {
        $this->fees = $fees;

        return $this;
}

    /**
     * @return Fee[]
     */
    public function get_fees(): array
    {
        return $this->fees;
    }

    /**
     * @return PaymentMethod[]
     */
    public function get_payment_methods(): array
    {
        return $this->paymentMethods;
    }

    /**
     * @param  PaymentMethod[]  $paymentMethods
     */
    public function set_payment_methods(array $paymentMethods)
    {
        $this->paymentMethods = $paymentMethods;
    }
}
