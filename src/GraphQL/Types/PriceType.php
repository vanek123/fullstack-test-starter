<?php

namespace App\GraphQL\Types;

use App\GraphQL\Types\CurrencyType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class PriceType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Price',
            'fields' => [
                'amount' => [
                    'type' => Type::float(),
                    'resolve' => fn($price) => $price['amount'],
                ],
                'currency' => [
                    'type' => new CurrencyType(),
                    'resolve' => fn($price) => [
                        'label' => $price['currency_label'],
                        'symbol' => $price['currency_symbol'],
                    ],
                ],
            ],
        ]);
    }
}
