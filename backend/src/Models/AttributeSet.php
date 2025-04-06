<?php

namespace App\Models;

abstract class AttributeSet
{
    protected string $id;
    protected string $name;
    protected string $type;

    /** @var AttributeItem[] */
    protected array $items;

    /**
     * Concrete classes must implement this constructor.
     */
    abstract public function __construct(array $data);

    /**
     * Factory method to create the correct attribute set instance.
     */
    public static function createFromData(array $data): self
    {
        return match (strtolower($data['type'] ?? 'text')) {
            'swatch' => new SwatchAttributeSet($data),
            default => new TextAttributeSet($data),
        };
    }

    /**
     * Return an array representation for GraphQL.
     */
    public function toArray(): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'type'  => $this->type,
            'items' => array_map(fn(AttributeItem $item) => $item->toArray(), $this->items),
        ];
    }

    /**
     * Initialize common data for child classes.
     */
    protected function initialize(array $data): void
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->type = $data['type'];
        $this->items = array_map(fn($item) => new AttributeItem($item), $data['items']);
    }
}
