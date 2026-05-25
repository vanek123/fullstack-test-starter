<?php

namespace App\GraphQL\Resolvers;

use App\Model\Order;
use InvalidArgumentException;

class OrderResolver
{
    public function placeOrder(array $args): bool
    {
        $orderData = $args['order'];
        $this->validate($orderData);
        return Order::create($orderData);
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
                }
            }
        }
    }
}