<?php

namespace Simpler\Http\Schemas;

final class QuotationSchema
{
    const SCHEMA = [
        '$schema'    => 'http://json-schema.org/draft-04/schema#',
        'title'      => 'order quote',
        'type'       => 'object',
        'additionalProperties' => true,
        'properties' => [
            'items'  => [
                'type'     => 'array',
                'required' => true,
                'minItems' => 1,
                'items'    => CartItemFragment::SCHEMA,
            ],
            'email' => [
                'type' => 'string'
            ],
            'coupon' => [
                'type' => 'string',
            ],
            'shipto' => [
                'type'       => 'object',
                'additionalProperties' => true,
                'properties' => [
                    'country'  => [
                        'type'     => 'string',
                        'required' => true,
                    ],
                    'state'    => [
                        'type' => 'string',
                    ],
                    'postcode' => [
                        'type'     => 'string',
                        'required' => true,
                    ],
                    'city'     => [
                        'type'     => 'string',
                        'required' => true,
                    ],
                    'address'  => [
                        'type'     => 'string',
                        'required' => true,
                    ],
                ],
            ],
        ],
    ];
}
