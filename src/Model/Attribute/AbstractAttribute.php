<?php

namespace App\Model\Attribute;

abstract class AbstractAttribute
{
    protected string $name;
    protected array $items;

    public function __construct(string $name, array $items)
    {
        $this->name = $name;
        $this->items = $items;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    abstract public function getType(): string;

    abstract public function isValidValue(string $value): bool;
}