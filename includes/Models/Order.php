<?php

namespace Simpler\Models;

final class Order
{
    /**
     * @var string
     */
    private $simpler_cart_id;
    /**
     * @var string
     */
    private $currency;
    /**
     * @var array
     */
    private $cart;
    /**
     * @var string
     */
    private $coupon;
    /**
     * @var int
     */
    private $simpler_discount;

    /**
     * @var OrderShipping
     */
    private $shipping;
    /**
     * @var PaymentMethod
     */
    private $paymentMethod;

    public function __construct($simpler_cart_id, $currency, $shipping, $coupon, $simpler_discount, array $cart, $paymentMethod = null)
    {
        $this->simpler_cart_id    = $simpler_cart_id;
        $this->currency           = $currency;
        $this->shipping           = $shipping;
        $this->simpler_discount   = $simpler_discount;
        $this->coupon             = $coupon;
        $this->cart               = $cart;
        $this->paymentMethod      = $paymentMethod;
    }

    public static function from_json(array $json)
    {
        $items = array_map(function ($el) {
            return CartItem::from_json($el);
        }, $json['cart']);

        $shipping = NULL;
        if (isset($json['shipping'])) {
            $shipping = OrderShipping::from_json($json['shipping']);
            if(isset($json['box_now']['locker_id'])){
                $shipping->set_box_now_locker_id($json['box_now']['locker_id']);
            }
        }

        $discount = 0;
        if (isset($json['simpler_discount'])) {
            $discount = $json['simpler_discount']['amount'];
        }

        return new Order(
            $json['simpler_cart_id'],
            $json['currency'],
            $shipping,
            $json['coupon'] ?? NULL,
            $discount,
            $items,
            isset($json['payment']) ? PaymentMethod::from_json($json['payment']) : null
        );
    }

    public function get_simpler_cart_id()
    {
        return $this->simpler_cart_id;
    }

    public function get_currency()
    {
        return $this->currency;
    }

    public function get_shipping()
    {
        return $this->shipping;
    }

    public function get_coupon()
    {
        return $this->coupon;
    }

    public function get_simpler_discount()
    {
        return Money::from_cents($this->simpler_discount);
    }

    public function get_cart()
    {
        return $this->cart;
    }

    public function get_payment_method()
    {
        return $this->paymentMethod;
    }
}
