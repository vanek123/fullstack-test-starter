<?php

namespace App\Model\Product;

class TechProduct extends AbstractProduct
{
    public function getType(): string
    {
        return 'tech';
    }

    public function validateAttributes(array $selectedAttributes): bool
    {
        if (empty($this->getAttributes()) && empty($selectedAttributes)) {
            return true;
        }

        return $this->checkValuesAgainstDatabase($selectedAttributes);
    }
}
