<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\Model\Currency;
use Brick\Math\BigDecimal;
use App\GraphQL\TypeRegistry;

class OrderResultType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'OrderResult',
            'description' => 'Result of placing an order',
            'fields' => [
                'orderId' => [
                    'type' => Type::nonNull(Type::int()),
                    'description' => 'The unique ID of the placed order',
                ],
                'totalAmount' => [
                    'type' => Type::nonNull(Type::float()),
                    'description' => 'The total amount of the order',
                    'resolve' => function (array $rootValue): float {
                        return (float) BigDecimal::of($rootValue['totalAmount'])->toFloat();
                    }
                ],
                'currency' => [
                    'type' => Type::nonNull(TypeRegistry::currency()),
                    'description' => 'The currency of the total amount',
                    'resolve' => function (array $rootValue): Currency {
                        return $rootValue['currency'];
                    }
                ],
                'message' => [
                    'type' => Type::string(),
                    'description' => 'A confirmation message',
                ],
            ],
        ]);
    }
}