<?php 
$host = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "scandiweb_test";

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo "Connected!\n";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage() . "\n");
}

$json_file = file_get_contents(__DIR__ . "/data.json");
$data = json_decode($json_file, true)['data'];

echo "Categories: " . count($data['categories']) . "\n";
echo "Products: " . count($data['products']) . "\n";

// Clear the tables before using seeder
$conn->exec("SET FOREIGN_KEY_CHECKS = 0");
$conn->exec("TRUNCATE TABLE attribute_items");
$conn->exec("TRUNCATE TABLE attributes");
$conn->exec("TRUNCATE TABLE product_gallery");
$conn->exec("TRUNCATE TABLE prices");
$conn->exec("TRUNCATE TABLE products");
$conn->exec("TRUNCATE TABLE categories");
$conn->exec("SET FOREIGN_KEY_CHECKS = 1");
echo "Tables cleared!\n";
$stmt = $conn->prepare("INSERT INTO categories (name) VALUES (:name)");

foreach($data['categories'] as $category) {
    $stmt->execute([':name' => $category['name']]);
    echo "Category added: " . $category['name'] . "\n";
}

$stmt = $conn->prepare("INSERT INTO products (id, name, in_stock, description, category, brand) 
VALUES(:id, :name, :in_stock, :description, :category, :brand)");

foreach($data['products'] as $product) {
    $stmt->execute([
        ':id' => $product['id'],
        ':name' => $product['name'],
        ':in_stock' => $product['inStock'] ? 1 : 0,
        ':description' => $product['description'],
        ':category' => $product['category'],
        ':brand' => $product['brand'],
        ]);
        echo "Product added: " . $product['name'] . "\n";
} 

$stmt = $conn->prepare("INSERT INTO prices (product_id, amount, currency_label, currency_symbol) 
VALUES(:product_id, :amount, :currency_label, :currency_symbol)");

foreach($data['products'] as $product) {
    foreach ($product['prices'] as $price) {
        $stmt->execute([
            ':product_id' => $product['id'],
            ':amount' => $price['amount'],
            ':currency_label' => $price['currency']['label'],
            ':currency_symbol' => $price['currency']['symbol'],
        ]);
    }
    
    echo "Price added: " . $product['name'] . "\n";
}

$stmt = $conn->prepare("INSERT INTO product_gallery (product_id, image_url, sort_order)
VALUES(:product_id, :image_url, :sort_order)");

foreach($data['products'] as $product) {
    foreach ($product['gallery'] as $index => $imageUrl) {
        $stmt->execute([
        ':product_id' => $product['id'],
        ':image_url' => $imageUrl,
        ':sort_order' => $index,
        ]);
    }
    echo "Gallery added: " . $product['name'] . "\n";
}

$stmtAttributes = $conn->prepare("INSERT INTO attributes (product_id, name, type)
VALUES(:product_id, :name, :type)");

$stmtItems = $conn->prepare("INSERT INTO attribute_items (attribute_id, display_value, value)
VALUES(:attribute_id, :display_value, :value)");

foreach($data['products'] as $product) {
    foreach ($product['attributes'] as $attribute) {
        $stmtAttributes->execute([
            ':product_id' => $product['id'],
            ':name' => $attribute['name'],
            ':type' => $attribute['type'],
        ]);
        $attributeId = $conn->lastInsertId();
    
        foreach($attribute['items'] as $item) {
            $stmtItems->execute([
                ':attribute_id' => $attributeId,
                ':display_value' => $item['displayValue'],
                ':value' => $item['value'],
            ]);
        }
    
    }       

}

echo "\nDone! All data has been added.\n";

?>