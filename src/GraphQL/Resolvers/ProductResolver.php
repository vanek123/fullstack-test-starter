<?php

namespace App\GraphQL\Resolvers;

use App\Model\Product\AbstractProduct;
use App\Model\Product\ProductFactory;
use App\Model\Attribute\AttributeFactory;

class ProductResolver
{
    // Issue #2 fixed: args from GraphQL-PHP are always array, never null
    public function resolve(array $args): array
    {
        $category = $args['category'] ?? null;
        $products = AbstractProduct::getAll($category);

        $result = [];
        foreach ($products as $product) {
            $result[] = $this->buildProduct($product);
        }

        return $result;
    }

    public function getProduct(array $args): ?AbstractProduct
    {
        $row = AbstractProduct::getById($args['id']);
        if ($row === false) {
            return null;
        }

        return $this->buildProduct($row);
    }

    private function buildProduct(array $product): AbstractProduct
    {
        $gallery = AbstractProduct::getProductGallery($product['id']);
        $attributes = AbstractProduct::getProductAttributes($product['id']);
        $prices = AbstractProduct::getProductPrices($product['id']);

        $attributeObjects = [];
        foreach ($attributes as $attribute) {
            $attributeObjects[] = AttributeFactory::create(
                $attribute['type'],
                $attribute['name'],
                $attribute['items']
            );
        }

        return ProductFactory::create(
            $product['id'],
            $product['name'],
            (bool) $product['in_stock'],
            $product['description'],
            $product['category'],
            $product['brand'],
            $gallery,
            $attributeObjects,
            $prices
        );
    }
}