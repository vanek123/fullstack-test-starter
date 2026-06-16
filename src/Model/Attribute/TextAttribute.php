<?php

namespace App\Model\Attribute;

class TextAttribute extends AbstractAttribute
{
    public function getType(): string
    {
        return 'text';
    }

    public function isValidValue(string $value): bool
    {
        return !empty(trim($value));
    }
}