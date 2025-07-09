<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\Model\Attribute\AttributeItem;

class AttributeItemType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'AttributeItem',
            'description' => 'An item within an attribute set',
            'fields' => [
                'id' => [
                    'type' => Type::nonNull(Type::int()),
                    'description' => 'The unique identifier of the attribute item',
                    'resolve' => function (AttributeItem $item): int {
                        return $item->getId();
                    },
                ], 
                'displayValue' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'The display value of the attribute item',
                    'resolve' => function (AttributeItem $item): string {
                        return $item->getDisplayValue();
                    },
                ],
                'value' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'The actual value of the attribute item',
                    'resolve' => function (AttributeItem $item): string {
                        return $item->getValue();
                    },
                ],
            ],
        ]);
    }
}