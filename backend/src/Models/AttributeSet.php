<?php
namespace App\Models;

abstract class AttributeSet {
    protected string $id;
    protected string $name;
    protected string $type;
    /** @var AttributeItem[] */
    protected array $items;

    /**
     * Concrete classes must implement a constructor accepting the raw data.
     */
    abstract public function __construct(array $data);

    /**
     * Factory method to create the correct attribute set instance.
     */
    public static function createFromData(array $data): self {
        if (isset($data['type']) && strtolower($data['type']) === 'swatch') {
            return new SwatchAttributeSet($data);
        }
        return new TextAttributeSet($data);
    }

    /**
     * Return an array representation for GraphQL.
     */
    public function toArray(): array {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'type'  => $this->type,
            'items' => array_map(fn(AttributeItem $item) => $item->toArray(), $this->items),
        ];
    }
}
