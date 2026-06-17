<?php

namespace App\Model\Repository;

use App\Model\AbstractModel;
use App\Model\Attribute\AttributeFactory;
use App\Model\Product\AbstractProduct;
use App\Model\Product\ProductFactory;
use PDO;

class ProductRepository extends AbstractModel
{
    private ProductFactory $productFactory;
    private AttributeFactory $attributeFactory;

    public function __construct(PDO $db)
    {
        parent::__construct($db);
        $this->productFactory = new ProductFactory();
        $this->attributeFactory = new AttributeFactory();
    }

    public function getAll(?string $category = null): array
    {
        $rows = $this->fetchAllRows($category);

        $result = [];
        foreach ($rows as $row) {
            $result[] = $this->buildProduct($row);
        }

        return $result;
    }

    public function getById(string $id): ?AbstractProduct
    {
        $row = $this->fetchOneRow($id);
        if ($row === false) {
            return null;
        }

        return $this->buildProduct($row);
    }

    private function buildProduct(array $product): AbstractProduct
    {
        $attributeObjects = [];
        foreach ($product['attributes'] as $attribute) {
            $attributeObjects[] = $this->attributeFactory->create(
                $attribute['type'],
                $attribute['name'],
                $attribute['items']
            );
        }

        return $this->productFactory->create(
            $product['id'],
            $product['name'],
            (bool) $product['in_stock'],
            $product['description'],
            $product['category'],
            $product['brand'],
            $product['gallery'],
            $attributeObjects,
            $product['prices']
        );
    }

    private function fetchAllRows(?string $category = null): array
    {
        $db = $this->db;

        $sql = "SELECT * FROM products";
        if ($category !== null && $category !== 'all') {
            $sql .= " WHERE category = :category";
            $stmt = $db->prepare($sql);
            $stmt->execute([':category' => $category]);
        } else {
            $stmt = $db->query($sql);
        }

        $productsRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (empty($productsRows)) {
            return [];
        }

        $productIds = array_column($productsRows, 'id');
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));

        $gStmt = $db->prepare(
            "SELECT product_id, image_url FROM product_gallery
            WHERE product_id IN ($placeholders) ORDER BY sort_order"
        );
        $gStmt->execute($productIds);
        $galleries = $gStmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);

        $pStmt = $db->prepare(
            "SELECT product_id, amount, currency_label, currency_symbol
            FROM prices WHERE product_id IN ($placeholders)"
        );
        $pStmt->execute($productIds);
        $prices = $pStmt->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_ASSOC);

        $aStmt = $db->prepare(
            "SELECT a.product_id, a.id as attr_id, a.name, a.type,
            ai.display_value, ai.value
            FROM attributes a
            LEFT JOIN attribute_items ai ON ai.attribute_id = a.id
            WHERE a.product_id IN ($placeholders)"
        );
        $aStmt->execute($productIds);
        $attributeRows = $aStmt->fetchAll(PDO::FETCH_ASSOC);

        $attributes = [];
        foreach ($attributeRows as $row) {
            $pId = $row['product_id'];
            $aId = $row['attr_id'];

            if (!isset($attributes[$pId][$aId])) {
                $attributes[$pId][$aId] = [
                    'id' => $aId,
                    'name' => $row['name'],
                    'type' => $row['type'],
                    'items' => [],
                ];
            }

            if ($row['display_value']) {
                $attributes[$pId][$aId]['items'][] = [
                    'display_value' => $row['display_value'],
                    'value' => $row['value'],
                ];
            }
        }

        $result = [];
        foreach ($productsRows as $product) {
            $id = $product['id'];

            $result[] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'in_stock' => $product['in_stock'],
                'description' => $product['description'],
                'category' => $product['category'],
                'brand' => $product['brand'],
                'gallery' => isset($galleries[$id])
                    ? array_column($galleries[$id], 'image_url')
                    : [],
                'attributes' => isset($attributes[$id])
                    ? array_values($attributes[$id])
                    : [],
                'prices' => $prices[$id] ?? [],
            ];
        }

        return $result;
    }

    private function fetchOneRow(string $id): array|false
    {
        $db = $this->db;
        $stmt = $db->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            return false;
        }

        $gStmt = $db->prepare(
            "SELECT image_url FROM product_gallery
            WHERE product_id = :id ORDER BY sort_order"
        );
        $gStmt->execute([':id' => $id]);
        $gallery = array_column($gStmt->fetchAll(PDO::FETCH_ASSOC), 'image_url');

        $pStmt = $db->prepare(
            "SELECT amount, currency_label, currency_symbol
            FROM prices WHERE product_id = :id"
        );
        $pStmt->execute([':id' => $id]);
        $prices = $pStmt->fetchAll(PDO::FETCH_ASSOC);

        $aStmt = $db->prepare(
            "SELECT a.id as attr_id, a.name, a.type,
            ai.display_value, ai.value
            FROM attributes a
            LEFT JOIN attribute_items ai ON ai.attribute_id = a.id
            WHERE a.product_id = :id"
        );
        $aStmt->execute([':id' => $id]);
        $attributeRows = $aStmt->fetchAll(PDO::FETCH_ASSOC);

        $attributes = [];
        foreach ($attributeRows as $row) {
            $aId = $row['attr_id'];

            if (!isset($attributes[$aId])) {
                $attributes[$aId] = [
                    'id' => $aId,
                    'name' => $row['name'],
                    'type' => $row['type'],
                    'items' => [],
                ];
            }

            if ($row['display_value']) {
                $attributes[$aId]['items'][] = [
                    'display_value' => $row['display_value'],
                    'value' => $row['value'],
                ];
            }
        }

        $product['gallery'] = $gallery;
        $product['prices'] = $prices;
        $product['attributes'] = array_values($attributes);

        return $product;
    }
}
