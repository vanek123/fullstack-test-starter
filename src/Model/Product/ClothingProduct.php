<?php

namespace App\Model\Product;

class ClothingProduct extends AbstractProduct
{
    public function getType(): string
    {
        return 'clothing';
    }

    public function validateAttributes(array $selectedAttributes): bool
    {
        if (empty($selectedAttributes)) {
            return false;
        }

        return $this->checkValuesAgainstDatabase($selectedAttributes);
    }
}
