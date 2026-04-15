<?php

namespace App\Model\Product;

class ProductFactory
{
    public static function create(string $id, string $name, bool $inStock, string $description, string $category, string $brand, array $gallery, array $attributes, array $prices): AbstractProduct
    {
        $types = [
            'clothes' => ClothingProduct::class,
            'tech' => TechProduct::class,
        ];

        $class = $types[$category] ?? TechProduct::class;
        return new $class($id, $name, $inStock, $description, $category, $brand, $gallery, $attributes, $prices);
    }
}

?>