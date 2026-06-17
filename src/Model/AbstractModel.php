<?php

namespace App\Model;

use PDO;

abstract class AbstractModel
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }
}
