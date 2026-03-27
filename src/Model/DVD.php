<?php 
namespace App\Model;

class DVD extends Product 
{

    protected float $size;

    public function __construct(int $id, string $name, float $size)
    {
        parent::__construct($id, $name);
        $this->size = $size;
    }

    public function getAttributes(): array 
    {
        return ['size' => $this->size];
    }

}

?>