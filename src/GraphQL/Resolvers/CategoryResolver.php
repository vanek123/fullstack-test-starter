<?php

namespace App\GraphQL\Resolvers;

use App\Model\Category;

class CategoryResolver
{
    private Category $category;

    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    public function resolve(): array
    {
        return $this->category->getAll();
    }
}