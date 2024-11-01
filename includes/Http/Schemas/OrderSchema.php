<?php

namespace Simpler\Http\Schemas;

final class OrderSchema
{
    const SCHEMA = [
        '$schema'              => 'http://json-schema.org/draft-04/schema#',
        'title'                => 'order',
        'type'                 => 'object',
        'additionalProperties' => true,
        'properties'           => [
            'user'  => [
                'description'          => 'User entry',
                'type'                 => 'object',
                'required'             => true,
                'additionalProperties' => true,
                'properties'           => [
                    'email'      => [
                        'type'     => 'string',
                        'required' => true
                    ],
                    'first_name' => [
                        'type'     => 'string',
                        'required' => true,
                    ],
                    'last_name'  => [
                        'type'     => 'string',
                        'required' => true
                    ],
                ]
            ],
            'order' => [
                'type'                 => 'object',
                'required'             => false,
                'additionalProperties' => true,
                'properties'           => [
                    'simpler_cart_id'    => [
                        'type'     => 'string',
                        'required' => true
                    ],
                    'currency'           => [
                        'type'     => 'string',
                        'required' => true
                    ],
                    'cart'               => [
                        'type'     => 'array',
                        'minItems' => 1,
                        'required' => true,
                        'items'    => CartItemFragment::SCHEMA,
                    ],
                    'shipping' => [
                        'type' => 'object',
                        'required' => false,
                        'additionalProperties' => true,
                        'properties' => [
                            'rate_id' => [
                                'type' => 'string',
                                'required' => true
                            ],
                            'title' => [
                                'type' => 'string',
                                'required' => false
                            ],
                            'cost_cents' => [
                                'type' => 'number',
                                'required' => false
                            ],
                            'pickup_location_id' => [
                                'type' => 'string',
                                'required' => 'false'
                            ]
                        ]
                    ],
                    'shipto'             => [
                        'type'                 => 'object',
                        'required'             => false,
                        'additionalProperties' => true,
                        'properties'           => [
                            'recipient' => [
                                'type'     => 'string',
                                'required' => true
                            ],
                            'phone'     => [
                                'type'     => 'string',
                                'required' => true
                            ],
                            'address'   => [
                                'type'     => 'string',
                                'required' => true
                            ],
                            'city'      => [
                                'type'     => 'string',
                                'required' => true
                            ],
                            'postcode'  => [
                                'type'     => 'string',
                                'required' => true
                            ],
                            'state'     => [
                                'type' => 'string',
                            ],
                            'country'   => [
                                'type'     => 'string',
                                'required' => true
                            ],
                            'notes'     => [
                                'type'     => 'string',
                                'required' => true
                            ]
                        ]
                    ],
                    'coupon' => [
                        'type' => 'string'
                    ],
                    'simpler_discount' => [
                        'type' => 'object',
                        'additionalProperties' => true,
                        'required' => false,
                        'properties' => [
                            'amount' => [
                                'type' => 'number',
                                'required' => true
                            ]
                        ]
                    ]
                ]
            ],
            'invoice' => [
                'type' => 'object',
                'additionalProperties' => true,
                'required' => false,
                'properties' => [
                    'tax_id' => [
                        'type' => 'string',
                        'required' => true
                    ],
                    'company_name' => [
                        'type' => 'string',
                        'required' => true
                    ],
                    'activity' => [
                        'type' => 'string',
                        'required' => true
                    ],
                    'tax_authority' => [
                        'type' => 'string',
                        'required' => true,
                    ],
                    'company_address' => [
                        'type' => 'string',
                        'required' => true
                    ]
                ],
            ]
        ]
    ];
}
