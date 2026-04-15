<?php
namespace App\Model\Product;

use App\Database;

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

    public function __construct(string $id, string $name, bool $inStock, string $description, string $category, string $brand, array $gallery, array $attributes, array $prices)
    {
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

    public static function getAll(): array {
        $pdo = Database::connect();
        $stmt = $pdo->query("SELECT * FROM products");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function getProductGallery(string $productId) : array {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT image_url FROM product_gallery WHERE product_id = :id ORDER BY sort_order");
        $stmt->execute([':id' => $productId]);
        return array_column($stmt->fetchAll(\PDO::FETCH_ASSOC), 'image_url');
    }

    public static function getProductAttributes(string $productId): array 
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM attributes WHERE product_id = :id");
        $stmt->execute([':id' => $productId]);
        $attributes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        foreach($attributes as &$attribute)
        {
            $stmt2 = $pdo->prepare("SELECT display_value, value FROM attribute_items WHERE attribute_id = :id");
            $stmt2->execute([':id' => $attribute['id']]);
            $attribute['items'] = $stmt2->fetchAll(\PDO::FETCH_ASSOC);
        }

        return $attributes;
    }

    public static function getProductPrices(string $productId): array
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM prices WHERE product_id = :id");
        $stmt->execute([':id' => $productId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
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
    
}
?>