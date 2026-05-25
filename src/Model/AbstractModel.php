<?php

namespace App\Model;

use App\Database;

use PDO;

abstract class AbstractModel
{
    protected static function getDb(): PDO
    {
        return Database::connect();
    }
}