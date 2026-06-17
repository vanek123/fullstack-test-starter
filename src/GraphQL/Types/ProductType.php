<?php

namespace App\GraphQL\Types;

use App\GraphQL\Types\AttributeType;
use App\GraphQL\Types\PriceType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class ProductType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'Product',
            'fields' => [
                'id' => [
                    'type' => Type::string(),
                    'resolve' => fn($product) => $product->getId(),
                ],
                'name' => [
                    'type' => Type::string(),
                    'resolve' => fn($product) => $product->getName(),
                ],
                'inStock' => [
                    'type' => Type::boolean(),
                    'resolve' => fn($product) => $product->getInStock(),
                ],
                'gallery' => [
                    'type' => Type::listOf(Type::string()),
                    'resolve' => fn($product) => $product->getGallery(),
                ],
                'description' => [
                    'type' => Type::string(),
                    'resolve' => fn($product) => $product->getDescription(),
                ],
                'category' => [
                    'type' => Type::string(),
                    'resolve' => fn($product) => $product->getCategory(),
                ],
                'attributes' => [
                    'type' => Type::listOf(new AttributeType()),
                    'resolve' => fn($product) => $product->getAttributes(),
                ],
                'prices' => [
                    'type' => Type::listOf(new PriceType()),
                    'resolve' => fn($product) => $product->getPrices(),
                ],
                'brand' => [
                    'type' => Type::string(),
                    'resolve' => fn($product) => $product->getBrand(),
                ],
                'type' => [
                    'type' => Type::string(),
                    'resolve' => fn($product) => $product->getType(),
                ],
            ],
        ]);
    }
}
