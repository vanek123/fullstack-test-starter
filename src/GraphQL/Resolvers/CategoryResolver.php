<?php

namespace App\GraphQL\Resolvers;

use App\Model\Category;

class CategoryResolver
{
    public function resolve(): array
    {
        return Category::getAll();
    }
}