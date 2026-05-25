<?php 

namespace App;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;
    
    public static function connect(): PDO
    {
        if (self::$instance === null) {
            $host = $_ENV['DB_HOST'];
            $username = $_ENV['DB_USER'];
            $password = $_ENV['DB_PASS'];
            $dbname = $_ENV['DB_NAME'];

            self::$instance = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $username, 
                $password,
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        }

        return self::$instance;
    }
}
