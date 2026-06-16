<?php

namespace App\Model\Attribute;

class SwatchAttribute extends AbstractAttribute
{
    public function getType(): string
    {
        return 'swatch';
    }

    public function isValidValue(string $value): bool
    {
        return (bool) preg_match('/^#[0-9A-Fa-f]{6}$/', $value);
    }
}