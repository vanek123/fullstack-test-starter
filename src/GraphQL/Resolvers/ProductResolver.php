<?php

namespace App\GraphQL\Resolvers;

use App\Model\Product\AbstractProduct;
use App\Model\Product\ProductFactory;
use App\Model\Attribute\AttributeFactory;
use App\Model\Repository\ProductRepository;

class ProductResolver
{
    private ProductRepository $productRepository;
    private ProductFactory $productFactory;
    private AttributeFactory $attributeFactory;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
        $this->productFactory = new ProductFactory();
        $this->attributeFactory = new AttributeFactory();
    }

    // Get products filtered by category, or all products if no category specified
    public function resolve(array $args): array
    {
        $category = $args['category'] ?? null;
        $products = $this->productRepository->getAll($category);

        $result = [];
        foreach ($products as $product) {
            $result[] = $this->buildProduct($product);
        }

        return $result;
    }

    public function getProduct(array $args): ?AbstractProduct
    {
        $row = $this->productRepository->getById($args['id']);
        if ($row === false) {
            return null;
        }

        return $this->buildProduct($row);
    }

    private function buildProduct(array $product): AbstractProduct
    {
        // gallery, attributes, prices уже в $product — не нужны отдельные запросы!
        $gallery = $product['gallery'] ?? [];
        $attributes = $product['attributes'] ?? [];
        $prices = $product['prices'] ?? [];

        $attributeObjects = [];
        foreach ($attributes as $attribute) {
            $attributeObjects[] = $this->attributeFactory->create(
                $attribute['type'],
                $attribute['name'],
                $attribute['items']
            );
        }

        return $this->productFactory->create(
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