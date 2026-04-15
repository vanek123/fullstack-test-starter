<?php 
namespace App\Model;
use App\Database;

class Category {
    public static function getAll(): array
    {
        $pdo = Database::connect();
        $stmt = $pdo->query("SELECT name FROM categories");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
?>
