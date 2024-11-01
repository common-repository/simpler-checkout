<?php

namespace Simpler\Models;

class QuotationRequest
{
    /**
     * @var string
     */
    private $couponCode = '';
    /**
     * @var string
     */
    private $userEmail = '';
    /**
     * @var CartItem[]
     */
    private $items;
    /**
     * @var Address
     */
    private $shippingAddress;


    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @param  string  $couponCode
     *
     * @return QuotationRequest
     */
    public function set_coupon_code(string $couponCode = '')
    {
        $this->couponCode = $couponCode;

        return $this;
    }

    /**
     * @param  Address  $address
     *
     * @return QuotationRequest
     */
    public function set_shipping_address(Address $address = null)
    {
        $this->shippingAddress = $address;

        return $this;
    }

    /**
     * @param string $email
     * @return QuotationRequest
     */
    public function set_user_email(string $email = '')
    {
        $this->userEmail = $email;
        return $this;
    }

    /**
     * @return Address
     */
    public function get_shipping_address()
    {
        return $this->shippingAddress;
    }

    /**
     * @return CartItem[]
     */
    public function get_items(): array
    {
        return $this->items;
    }

    /**
     * @return string
     */
    public function get_coupon_code(): string
    {
        return $this->couponCode;
    }

    /**
     * @return string
     */
    public function get_user_email(): string
    {
        return $this->userEmail;
    }
}
