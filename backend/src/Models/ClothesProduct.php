<?php

namespace App\Models;

class ClothesProduct extends Product
{
    public function __construct(array $data)
    {
        $this->initialize($data);
    }
}
