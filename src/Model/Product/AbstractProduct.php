<?php

namespace App\Model\Product;

abstract class AbstractProduct
{
    protected string $id;
    protected string $name;
    protected bool $inStock;
    protected string $description;
    protected string $category;
    protected string $brand;
    protected array $gallery;
    protected array $attributes;
    protected array $prices;

    public function __construct(
        string $id,
        string $name,
        bool $inStock,
        string $description,
        string $category,
        string $brand,
        array $gallery,
        array $attributes,
        array $prices
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->inStock = $inStock;
        $this->description = $description;
        $this->category = $category;
        $this->brand = $brand;
        $this->gallery = $gallery;
        $this->attributes = $attributes;
        $this->prices = $prices;
    }

    protected function checkValuesAgainstDatabase(array $selectedAttributes): bool
    {
        if (count($selectedAttributes) !== count($this->getAttributes())) {
            return false;
        }

        $selectedMap = array_column($selectedAttributes, 'value', 'name');

        foreach ($this->getAttributes() as $attribute) {
            $name = $attribute->getName();

            if (!isset($selectedMap[$name])) {
                return false;
            }
            if (!$attribute->isValidValue($selectedMap[$name])) {
                return false;
            }
        }

        return true;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getInStock(): bool
    {
        return $this->inStock;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function getGallery(): array
    {
        return $this->gallery;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getPrices(): array
    {
        return $this->prices;
    }

    abstract public function getType(): string;

    abstract public function validateAttributes(array $selectedAttributes): bool;
}
