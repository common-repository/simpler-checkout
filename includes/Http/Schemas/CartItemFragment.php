<?php

namespace Simpler\Http\Schemas;

final class CartItemAttributesFragment
{
    const SCHEMA = [
        'type' => 'array',
        'required' => false,
        'items' => [
            'type' => 'object',
            'required' => true,
            'additionalProperties' => true,
            'properties' => [
                'key' => [
                    'type' => 'string',
                    'required' => true
                ],
                'value' => [
                    'type' => 'string',
                    'required' => true
                ]
            ]
        ]
    ];
};

final class CartItemFragment
{
    const SCHEMA = [
        'type'                 => 'object',
        'required'             => true,
        'additionalProperties' => true,
        'properties'           => [
            'product_id' => [
                'type'     => 'number',
                'required' => true
            ],
            'quantity'   => [
                'type'     => 'number',
                'required' => true,
                'min'      => 1
            ],
            'bundled' => [
                'type' => 'array',
                'required' => false,
                'items' => [
                    'type' => 'object',
                    'required' => true,
                    'additionalProperties' => true,
                    'properties' => [
                        'product_id' => [
                            'type' => 'string',
                            'required' => true
                        ],
                        'quantity' => [
                            'type' => 'number',
                            'required' => true,
                        ],
                        'attributes' => CartItemAttributesFragment::SCHEMA,
                    ]
                ]
            ],
            'attributes' =>  CartItemAttributesFragment::SCHEMA
        ]
    ];
}
