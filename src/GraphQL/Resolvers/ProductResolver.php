<?php

namespace App\GraphQL\Resolvers;

use App\Model\Product\AbstractProduct;
use App\Model\Product\ProductFactory;
use App\Model\Attribute\AttributeFactory;


class ProductResolver
{
    public function resolve(?array $args): array
    {
        $products = AbstractProduct::getAll();
        $result = [];

        foreach($products as $product)
        {
            $gallery = AbstractProduct::getProductGallery($product['id']);
            $attributes = AbstractProduct::getProductAttributes($product['id']);
            $prices = AbstractProduct::getProductPrices($product['id']);

            $attributeObjects = [];
            foreach ($attributes as $attribute) {
                $attributeObjects[] = AttributeFactory::create(
                    $attribute['type'],
                    $attribute['name'],
                    $attribute['items'],
                );
            }

            $result[] = ProductFactory::create($product['id'], $product['name'], $product['in_stock'], $product['description'], $product['category'], $product['brand'], $gallery, $attributeObjects, $prices);

        }

        return $result;
    }
}

?>