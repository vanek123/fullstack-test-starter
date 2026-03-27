<?php 
namespace App\Model;

class Furniture extends Product 
{
    protected float $height;
    protected float $width;
    protected float $length;

    public function __construct(int $id, string $name, float $height, float $width, float $length) 
    {
        parent::__construct($id, $name);
        $this->height = $height;
        $this->width = $width;
        $this->length = $length;
    }

    public function getAttributes(): array 
    {
        return [
            'height' => $this->height,
            'width' => $this->width,
            'length' => $this->length
             ];
    }

}

?>