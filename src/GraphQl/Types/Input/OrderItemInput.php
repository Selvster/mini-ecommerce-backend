<?php

namespace App\GraphQL\Types\Input;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use App\GraphQL\TypeRegistry; 

class OrderItemInput extends InputObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'OrderItemInput',
            'description' => 'Represents a single product item within an order',
            'fields' => [
                'productId' => Type::nonNull(Type::id()),
                'quantity' => Type::nonNull(Type::int()),
                'selectedAttributes' => [
                    'type' => Type::listOf(Type::nonNull(TypeRegistry::selectedAttributeInput())),
                    'description' => 'List of selected attributes for this product item',
                    'defaultValue' => [],
                ],
            ],
        ]);
    }
}