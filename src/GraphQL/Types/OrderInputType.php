<?php 

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class OrderInputType extends InputObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'OrderInput',
            'fields' => [
                'products' => [
                    'type' => Type::nonNull(Type::listOf(Type::nonNull(new ProductInputType()))),
                ]
            ],
        ]);
    }
}
