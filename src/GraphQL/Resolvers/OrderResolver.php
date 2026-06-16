<?php

namespace App\GraphQL\Resolvers;

use App\Model\Order;
use App\Model\Repository\ProductRepository;
use InvalidArgumentException;

class OrderResolver
{
    private Order $order;
    private ProductRepository $productRepository;

    public function __construct(Order $order, ProductRepository $productRepository)
    {
        $this->order = $order;
        $this->productRepository = $productRepository;
    }

    public function placeOrder(array $args): bool
    {
        $orderData = $args['order'];
        $this->validate($orderData);
        return $this->order->create($orderData);
    }

    private function validate(array $orderData): void
    {
        if (empty($orderData['products']) || !is_array($orderData['products'])) {
            throw new InvalidArgumentException('Order must contain at least one product.');
        }

        foreach ($orderData['products'] as $index => $product) {
            $pos = $index + 1;

            if (empty($product['id']) || !is_string($product['id'])) {
                throw new InvalidArgumentException("Product #{$pos}: id must be a non-empty string.");
            }

            if (!isset($product['quantity']) || !is_int($product['quantity']) || $product['quantity'] < 1) {
                throw new InvalidArgumentException("Product #{$pos}: quantity must be a positive integer.");
            }

            if (isset($product['attributes']) && !is_array($product['attributes'])) {
                throw new InvalidArgumentException("Product #{$pos}: attributes must be an array.");
            }

            $productObj = (new ProductResolver($this->productRepository))->getProduct(['id' => $product['id']]);
            $productAttributes = $productObj?->getAttributes() ?? [];

            if (!empty($product['attributes'])) {
                foreach ($product['attributes'] as $attr) {
                    if (empty($attr['name']) || !is_string($attr['name'])) {
                        throw new InvalidArgumentException(
                            "Product #{$pos}: each attribute must have a string attributeId."
                        );
                    }
                    if (!isset($attr['value']) || !is_string($attr['value'])) {
                        throw new InvalidArgumentException(
                            "Product #{$pos}: each selectedAttribute must have a string value."
                        );
                    }

                    foreach ($productAttributes as $attributeObj) {
                        if ($attributeObj->getName() === $attr['name']) {
                            if (!$attributeObj->isValidValue($attr['value'])) {
                                throw new InvalidArgumentException(
                                    "Product #{$pos}: invalid value '{$attr['value']}' for attribute '{$attr['name']}'."
                                );
                            }
                        }
                    }
                }
            }

            if ($productObj !== null && !$productObj->validateAttributes($product['attributes'] ?? [])) {
                throw new InvalidArgumentException("Invalid attributes for product {$product['id']}");
            }
        }
    }
}