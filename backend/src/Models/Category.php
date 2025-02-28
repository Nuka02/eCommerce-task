<?php

namespace App\Models;

class Category {
    protected string $id;
    protected string $name;

    public function __construct(string $id, string $name) {
        $this->id = $id;
        $this->name = $name;
    }

    public static function createFromData(array $data): self {
        return new self($data['id'], $data['name']);
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}
