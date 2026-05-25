<?php

namespace App\Model\Attribute;

class TextAttribute extends AbstractAttribute
{
    public function getType(): string {
        return 'text';
    }
}
