<?php

namespace App\Models;

class DefaultProduct extends Product
{
    public function __construct(array $data)
    {
        $this->initialize($data);
    }
}
