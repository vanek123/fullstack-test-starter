<?php

namespace App\Model;

class Category extends AbstractModel
{
    public function getAll(): array
    {
        $db = $this->db;
        $stmt = $db->query("SELECT name FROM categories");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}