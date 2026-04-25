<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class ProductInputType extends InputObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'ProductInput',
            'fields' => [
                'id' => Type::nonNull(Type::string()),
                'quantity' => Type::nonNull(Type::int()),
                'attributes' => Type::listOf(new AttributeInputType()),
            ],
        ]);
    }
}

?>