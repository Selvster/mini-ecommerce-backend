<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\Model\Currency;

class CurrencyType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Currency',
            'description' => 'A currency type',
            'fields' => [
                'id' => [
                    'type' => Type::nonNull(Type::id()),
                    'description' => 'The unique identifier of the currency',
                    'resolve' => function (Currency $currency): string {
                        return $currency->getId();
                    }
                ],
                'label' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'The label of the currency',
                    'resolve' => function (Currency $currency): string {
                        return $currency->getLabel();
                    }
                ],
                'symbol' => [
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'The symbol of the currency',
                    'resolve' => function (Currency $currency): string {
                        return $currency->getSymbol();
                    }
                ],
            ],
        ]);
    }
}