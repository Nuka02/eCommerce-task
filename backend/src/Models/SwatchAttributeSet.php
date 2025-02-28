<?php
namespace App\Models;

class SwatchAttributeSet extends AttributeSet {
    public function __construct(array $data) {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->type = $data['type'];
        $this->items = array_map(fn($itemData) => new AttributeItem($itemData), $data['items']);
    }
}
