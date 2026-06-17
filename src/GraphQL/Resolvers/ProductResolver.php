<?php

namespace App\GraphQL\Resolvers;

use App\Model\Product\AbstractProduct;
use App\Model\Repository\ProductRepository;

class ProductResolver
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function resolve(array $args): array
    {
        $category = $args['category'] ?? null;
        return $this->productRepository->getAll($category);
    }

    public function getProduct(array $args): ?AbstractProduct
    {
        return $this->productRepository->getById($args['id']);
    }
}
