<?php

namespace Simpler\Http\Schemas;

final class OrderQuoteSchema
{
    public static $schema
    = [
        '$schema'              => 'http://json-schema.org/draft-04/schema#',
        'title'                => 'order quote',
        'type'                 => 'object',
        'additionalProperties' => false,
        'properties'           => [
            'items'  => [
                'type'     => 'array',
                'required' => true,
                'minItems' => 1,
                'items'    => [
                    'type'                 => 'object',
                    'required'             => true,
                    'additionalProperties' => false,
                    'properties'           => [
                        'product_id'       => [
                            'type'     => 'number',
                            'min'      => 1,
                            'required' => true,
                        ],
                        'quantity' => [
                            'type'     => 'number',
                            'required' => true,
                            'min'      => 1,
                        ],
                    ],
                ],
            ],
            'coupon' => [
                'type' => 'string',
            ],
        ],
    ];
}
