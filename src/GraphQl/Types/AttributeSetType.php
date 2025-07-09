<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\Model\Attribute\AbstractAttribute;
use App\Model\Attribute\AttributeItem;
use App\GraphQL\TypeRegistry; 

class AttributeSetType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'AttributeSet',
            'description' => 'A set of product attributes (e.g., Size, Color)',
            'fields' => [
                'id' => [
                    'type' => Type::nonNull(Type::id()),
                    'description' => 'The unique identifier of the attribute set',
                    'resolve' => function (AbstractAttribute $attributeSet): string {
                        return $attributeSet->getId();
                    }
                ],
                'name' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'The name of the attribute set',
                    'resolve' => function (AbstractAttribute $attributeSet): string {
                        return $attributeSet->getName();
                    }
                ],
                'type' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'The type of the attribute set (e.g., Size, Color)',
                    'resolve' => function (AbstractAttribute $attributeSet): string {
                        return $attributeSet->getType();
                    }
                ],
                'items' => [
                    'type' => Type::nonNull(Type::listOf(Type::nonNull(TypeRegistry::attributeItem()))),
                    'description' => 'The available options for this attribute set',
                    'resolve' => function (AbstractAttribute $attributeSet): array {
                        return $attributeSet->getItems();
                    }
                ],
            ],
        ]);
    }
}