<?php

namespace App\Model\Product;

class TechProduct extends AbstractProduct
{
    public function getType(): string {
        return 'tech';
    }
}

?>