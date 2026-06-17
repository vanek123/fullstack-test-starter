<?php

namespace App\Model\Attribute;

class AttributeFactory
{
    public function create(string $type, string $name, array $items): AbstractAttribute
    {
        $types = [
            'text' => TextAttribute::class,
            'swatch' => SwatchAttribute::class,
        ];

        $class = $types[$type] ?? TextAttribute::class;
        return new $class($name, $items);
    }
}
