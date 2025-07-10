<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\Model\Order\OrderItem;
use App\GraphQL\TypeRegistry; // Ensure TypeRegistry is used

class OrderItemType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'OrderItem',
            'description' => 'An item within an order',
            'fields' => [
                'id' => [
                    'type' => Type::nonNull(Type::id()),
                    'description' => 'The unique identifier for the order item',
                    'resolve' => function (OrderItem $item): string {
                        return $item->getId();
                    }
                ],
                'productId' => [
                    'type' => Type::nonNull(Type::id()),
                    'description' => 'The ID of the product in this order item',
                    'resolve' => function (OrderItem $item): string {
                        return $item->getProductId();
                    }
                ],
                'quantity' => [
                    'type' => Type::nonNull(Type::int()),
                    'description' => 'The quantity of the product in this order item',
                    'resolve' => function (OrderItem $item): int {
                        return $item->getQuantity();
                    }
                ],
                'selectedAttributes' => [
                    'type' => Type::listOf(Type::nonNull(TypeRegistry::selectedAttribute())), 
                    'description' => 'Selected attributes for this product item',
                    'resolve' => function (OrderItem $item): ?array {
                        // Return as array of objects, each with name and value
                        return $item->getSelectedAttributes();
                    }
                ],
            ],
        ]);
    }
}