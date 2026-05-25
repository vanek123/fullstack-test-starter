<?php

namespace App\Model\Attribute;

class SwatchAttribute extends AbstractAttribute
{
    public function getType(): string 
    {
        return 'swatch';
    }
}