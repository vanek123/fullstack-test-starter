<?php

namespace App\Model\Product;

class ClothingProduct extends AbstractProduct
{
    public function getType(): string 
    {
        return 'clothing';
    }
}