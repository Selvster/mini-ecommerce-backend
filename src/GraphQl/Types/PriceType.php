<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\Model\Price;
use App\Model\Currency;
use App\GraphQL\TypeRegistry; 

class PriceType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Price',
            'description' => 'Price of a product in a specific currency',
            'fields' => [
                'amount' => [
                    'type' => Type::nonNull(Type::float()),
                    'description' => 'The numerical amount of the price',
                    'resolve' => function (Price $price): float {
                        return (float) $price->getAmount()->toFloat();
                    }
                ],
                'currency' => [
                    'type' => Type::nonNull(TypeRegistry::currency()),
                    'description' => 'The currency of the price',
                    'resolve' => function (Price $price): Currency {
                        return $price->getCurrency();
                    }
                ],
            ],
        ]);
    }
}