<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\Model\Category;

class CategoryType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Category',
            'description' => 'A product category',
            'fields' => [
                'id' => [
                    'type' => Type::nonNull(Type::int()),
                    'description' => 'The unique identifier of the category',
                    'resolve' => function (Category $category): int {
                        return $category->getId();
                    }
                ],
                'name' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'The name of the category',
                    'resolve' => function (Category $category): string {
                        return $category->getName();
                    }
                ]
            ],
        ]);
    }
}