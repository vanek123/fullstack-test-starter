<?php

namespace App\Model;

class Category extends AbstractModel
{
    public static function getAll(): array
    {
        $db = self::getDb();
        $stmt = $db->query("SELECT name FROM categories");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}