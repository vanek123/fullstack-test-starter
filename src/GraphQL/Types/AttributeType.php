<?php

namespace App\GraphQL\Types;

use App\GraphQL\Types\AttributeItemType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class AttributeType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Attribute',
            'fields' => [
                'items' => [
                    'type' => Type::listOf(new AttributeItemType()),
                    'resolve' => fn($attr) => $attr->getItems(),
                ],
                'name' => [
                    'type' => Type::string(),
                    'resolve' => fn($attr) => $attr->getName(),
                ],
                'type' => [
                    'type' => Type::string(),
                    'resolve' => fn($attr) => $attr->getType(),
                ],
            ],
        ]);
    }
}
