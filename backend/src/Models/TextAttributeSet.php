<?php

namespace App\Models;

class TextAttributeSet extends AttributeSet
{
    public function __construct(array $data)
    {
        $this->type = 'text';
        $this->initialize($data);
    }
}
