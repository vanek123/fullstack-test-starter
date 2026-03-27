<?php
namespace App\Model;

class Book extends Product
{
    protected float $weight;

    public function __construct(int $id, string $name, float $weight)
    {
        parent::__construct($id, $name);
        $this->weight = $weight;
    }

    public function getAttributes(): array 
    {
        return ['weight' => $this->weight];
    }
}

?>
