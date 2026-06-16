<?php

namespace App\Model;

class Order extends AbstractModel
{
    public function create(array $orderData): bool
    {
        $pdo = $this->db;

        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO orders (created_at) VALUES (NOW())");
            $stmt->execute();
            $orderId = $pdo->lastInsertId();

            $stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, selected_attributes)
                VALUES (:order_id, :product_id, :quantity, :attributes)
            ");

            foreach ($orderData['products'] as $product) {
                $attributes = isset($product['attributes'])
                    ? json_encode($product['attributes'])
                    : null;

                $stmt->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $product['id'],
                    ':quantity' => $product['quantity'],
                    ':attributes' => $attributes,
                ]);
            }

            $pdo->commit();
            return true;
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}