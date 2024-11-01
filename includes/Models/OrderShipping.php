<?php

namespace Simpler\Models;

final class OrderShipping
{
    private $method_id;
    private $instance_id;
    private $method_title;
    private $cost_cents;
    private $pickup_location_id;
    private $box_now_locker_id;

    public function __construct($rate_id, $method_title, $cost_cents, $pickup_location_id)
    {
        $this->rate_id = $rate_id;
        list($method_id, $instance_id) = array_pad(explode(":", $rate_id), 2, '');
        $this->method_id = $method_id;
        $this->instance_id = $instance_id;
        $this->method_title = $method_title;
        $this->pickup_location_id = $pickup_location_id;
        $this->cost_cents = $cost_cents;
    }

    public static function from_json(array $json)
    {
        return new OrderShipping(
            $json['rate_id'],
            $json['title'] ?? '',
            $json['cost_cents'],
            $json['pickup_location_id'] ?? ''
        );
    }

    public function get_method_id()
    {
        return $this->method_id;
    }

    public function get_instance_id()
    {
        return $this->instance_id;
    }

    public function get_method_title()
    {
        return $this->method_title;
    }

    public function get_rate_id()
    {
        return $this->rate_id;
    }

    public function get_cost()
    {
        return Money::from_cents($this->cost_cents);
    }

    public function get_pickup_location_id()
    {
        return $this->pickup_location_id;
    }

    public function get_box_now_locker_id()
    {
        return $this->box_now_locker_id;
    }

    public function set_box_now_locker_id($locker_id)
    {
         $this->box_now_locker_id = $locker_id;
    }

}
