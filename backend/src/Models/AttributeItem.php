<?php

namespace App\Models;

class AttributeItem
{
    protected string $displayValue;
    protected string $value;
    protected ?string $id;

    public function __construct(array $data)
    {
        $this->displayValue = $data['display_value'];
        $this->value = $data['value'];
        $this->id = $data['id'] ?? null;
    }

    public function toArray(): array
    {
        return [
            'displayValue' => $this->displayValue,
            'value'        => $this->value,
            'id'           => $this->id,
        ];
    }
}
