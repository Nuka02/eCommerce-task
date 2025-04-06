<?php

namespace App\Models;

class SwatchAttributeSet extends AttributeSet
{
    public function __construct(array $data)
    {
        $this->type = 'swatch';
        $this->initialize($data);
    }
}
