<?php

namespace Simpler\Models;

final class CartItem
{
    /**
     * @var int
     */
    private $product_id;
    /**
     * @var int
     */
    private $quantity;
    /**
     * @var ProductAttribute[]
     */
    private $attrs;

    /**
     * @var array
     */
    private $bundle_configuration;

    public function __construct($product_id, $quantity, $attrs = [], $bundled = [])
    {
        $this->product_id              = $product_id;
        $this->quantity                = $quantity;
        $this->attrs                   = $attrs;
        $this->bundle_configuration    = $bundled;
    }

    public static function from_json(array $json)
    {
        $attrs = array_map(function ($el) {
            return new ProductAttribute($el['key'], $el['value']);
        }, $json['attributes'] ?? []);
        return new CartItem($json['product_id'], $json['quantity'], $attrs, $json['bundled'] ?? []);
    }

    public function get_product_id()
    {
        return absint($this->product_id);
    }

    public function get_quantity()
    {
        return absint($this->quantity);
    }

    public function get_attributes()
    {
        return $this->attrs;
    }

    public function get_attributes_array()
    {
        return array_reduce(
            $this->attrs,
            function ($acc, $el) {
                $acc[$el->get_key()] = $el->get_value();
                return $acc;
            },
            []
        );
    }

    public function get_bundle_configuration()
    {
        $config = $this->bundle_configuration;
        foreach ($config as $idx => $product) {
            if (!array_key_exists('attributes', $product)) {
                $config[$idx]['attributes'] = [];
            } else {
                $config[$idx]['attributes'] = array_reduce($product['attributes'], function ($acc, $el) {
                    $acc[$el['key']] = $el['value'];
                    return $acc;
                }, []);
            }
        }
        return $config;
    }
}
