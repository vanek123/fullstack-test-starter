<?php

namespace App\Model\Repository;

use App\Model\AbstractModel;

use PDO;

class ProductRepository extends AbstractModel
{
    public function getAll(?string $category = null): array
    {
        $db = $this->db;

        if ($category !== null && $category !== 'all') {
            $stmt = $db->prepare("SELECT * FROM products WHERE category = :category");
            $stmt->execute([':category' => $category]);
        } else {
            $stmt = $db->query("SELECT * FROM products");
        }

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(string $id): array|false
    {
        $db = $this->db;
        $stmt = $db->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getProductGallery(string $productId): array
    {
        $db = $this->db;
        $stmt = $db->prepare(
            "SELECT image_url FROM product_gallery WHERE product_id = :id ORDER BY sort_order"
        );
        $stmt->execute([':id' => $productId]);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'image_url');
    }

    public function getProductAttributes(string $productId): array
    {
        $db = $this->db;
        $stmt = $db->prepare("SELECT * FROM attributes WHERE product_id = :id");
        $stmt->execute([':id' => $productId]);
        $attributes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($attributes as &$attribute) {
            $stmt2 = $db->prepare(
                "SELECT display_value, value FROM attribute_items WHERE attribute_id = :id"
            );
            $stmt2->execute([':id' => $attribute['id']]);
            $attribute['items'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        }

        return $attributes;
    }

    public function getProductPrices(string $productId): array
    {
        $db = $this->db;
        $stmt = $db->prepare("SELECT * FROM prices WHERE product_id = :id");
        $stmt->execute([':id' => $productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}