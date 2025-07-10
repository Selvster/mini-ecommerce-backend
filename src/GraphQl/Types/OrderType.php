<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\Model\Order\Order;
use App\Model\Currency;
use App\GraphQL\TypeRegistry; // Ensure TypeRegistry is used
use Brick\Math\BigDecimal;

class OrderType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Order',
            'description' => 'A customer order',
            'fields' => [
                'id' => [
                    'type' => Type::nonNull(Type::id()),
                    'description' => 'The unique identifier for the order',
                    'resolve' => function (Order $order): string {
                        return $order->getId();
                    }
                ],
                'createdAt' => [
                    'type' => Type::nonNull(Type::string()), // Represent DateTime as string
                    'description' => 'The date and time the order was placed (ISO 8601 format)',
                    'resolve' => function (Order $order): string {
                        return $order->getCreatedAt()->format(\DateTimeInterface::ISO8601);
                    }
                ],
                'totalAmount' => [
                    'type' => Type::nonNull(Type::float()),
                    'description' => 'The total amount of the order',
                    'resolve' => function (Order $order): float {
                        return (float) $order->getTotalAmount()->toFloat();
                    }
                ],
                'currency' => [
                    'type' => Type::nonNull(TypeRegistry::currency()),
                    'description' => 'The currency of the order total',
                    'resolve' => function (Order $order): Currency {
                        return $order->getCurrency();
                    }
                ],
                'items' => [
                    'type' => Type::nonNull(Type::listOf(Type::nonNull(TypeRegistry::orderItem()))),
                    'description' => 'List of items in the order',
                    'resolve' => function (Order $order): array {
                        return $order->getItems();
                    }
                ],
            ],
        ]);
    }
}