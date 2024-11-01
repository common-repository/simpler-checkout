<?php

namespace Simpler\Models;

final class Address
{
    /**
     * @var string
     */
    private $recipient;
    /**
     * @var string
     */
    private $phone;
    /**
     * @var string
     */
    private $address;
    /**
     * @var string
     */
    private $city;
    /**
     * @var string
     */
    private $postcode;
    /**
     * @var string
     */
    private $state;
    /**
     * @var string
     */
    private $country;
    /**
     * @var string
     */
    private $notes;

    public function __construct($recipient, $phone, $address, $city, $postcode, $state, $country, $notes)
    {
        $this->recipient = $recipient;
        $this->phone     = $phone;
        $this->address   = $address;
        $this->city      = $city;
        $this->postcode  = $postcode;
        $this->state     = $state;
        $this->country   = $country;
        $this->notes     = $notes;
    }

    public static function from_json(array $json)
    {
        if (is_null($json)) {
            throw new \InvalidArgumentException("missing address data");
        }

        return new Address(
            $json['recipient'],
            $json['phone'],
            $json['address'],
            $json['city'],
            $json['postcode'],
            $json['state'] ?? '',
            $json['country'],
            $json['notes']
        );
    }

    public static function from_quotation_json(array $json)
    {
        return new Address(
            null,
            null,
            $json['address'],
            $json['city'],
            $json['postcode'],
            $json['state'] ?? '',
            $json['country'],
            null
        );
    }

    /**
     * returns the address represented as woo address props, optionally prefixed
     */
    public function to_address_props($prefix = '')
    {
        $pfx = strlen($prefix) > 0 ? "${prefix}_" : "";
        $addr = [
            "${pfx}country"   => $this->country,
            "${pfx}state"     => $this->state,
            "${pfx}postcode"  => $this->postcode,
            "${pfx}city"      => $this->city,
            "${pfx}address_1" => $this->address,
            "${pfx}address_2" => $this->notes,
            "${pfx}phone" => $this->phone
        ];

        $name = explode(' ', $this->recipient, 2);
        if (count($name) == 2) {
            $addr["${pfx}first_name"] = $name[0];
            $addr["${pfx}last_name"] = $name[1];
        } else if (count($name) == 1) {
            $addr["${pfx}first_name"] = '';
            $addr["${pfx}last_name"] = $name[0];
        }
        return $addr;
    }

    /**
     * returns the address ready to be ingested by WC_Customer#set_props
     */
    public function to_customer_address_props()
    {
        return $this->to_address_props('shipping') + $this->to_address_props('billing');
    }

    public function get_recipient()
    {
        return $this->recipient;
    }

    public function get_phone()
    {
        return $this->phone;
    }

    public function get_address()
    {
        return $this->address;
    }

    public function get_city()
    {
        return $this->city;
    }

    public function get_postcode()
    {
        return $this->postcode;
    }

    public function get_state()
    {
        return $this->state;
    }

    public function get_country()
    {
        return $this->country;
    }

    public function get_notes()
    {
        return $this->notes;
    }
}
