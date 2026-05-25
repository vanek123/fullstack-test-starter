<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class AttributeItemType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'AttributeItem',
            'fields' => [
                'displayValue' => [
                    'type' => Type::string(),
                    'resolve' => fn($item) => $item['display_value'],
                ],
                'value' => [
                    'type' => Type::string(),
                    'resolve' => fn($item) => $item['value'],
                ],
            ],
        ]);
    }
}
