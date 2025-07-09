<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use App\GraphQL\Types\Input\OrderItemInput; 

class OrderInputType extends InputObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'OrderInput',
            'description' => 'Input for placing a new order',
            'fields' => [
                'items' => [
                    'type' => Type::nonNull(Type::listOf(Type::nonNull(new OrderItemInput()))),
                    'description' => 'List of items in the order',
                ],
            ],
        ]);
    }
}