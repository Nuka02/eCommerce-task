<?php

namespace App\Models;

class TechProduct extends Product
{
    public function __construct(array $data)
    {
        $this->initialize($data);
    }
}
