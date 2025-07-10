<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class SelectedAttributeType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'SelectedAttribute',
            'description' => 'A selected attribute (name-value pair) for an order item',
            'fields' => [
                'name' => Type::nonNull(Type::string()),
                'value' => Type::nonNull(Type::string()),
            ],
        ]);
    }
}