<?php

namespace App\GraphQL\Types\Input;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class SelectedAttributeInput extends InputObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'SelectedAttributeInput',
            'description' => 'Represents a selected attribute for a product in an order item',
            'fields' => [
                'name' => Type::nonNull(Type::string()),
                'value' => Type::nonNull(Type::string()),
            ],
        ]);
    }
}