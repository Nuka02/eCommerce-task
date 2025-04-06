<?php

namespace App\Models;

abstract class Product
{
    protected string $id;
    protected string $name;
    protected bool $inStock;
    protected string $description;
    protected string $brand;
    protected ?string $category;
    protected array $gallery;
    /** @var AttributeSet[] */
    protected array $attributeSets;
    /** @var Price[] */
    protected array $prices;

    protected function initialize(array $data): void
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->inStock = (bool)$data['in_stock'];
        $this->description = $data['description'];
        $this->brand = $data['brand'];
        $this->category = $data['category_name'] ?? 'default';
        $this->gallery = $data['gallery'];
        $this->attributeSets = array_map(fn($attrSet) => AttributeSet::createFromData($attrSet), $data['attributes']);
        $this->prices = array_map(fn($priceData) => new Price($priceData), $data['prices']);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'inStock' => $this->inStock,
            'description' => $this->description,
            'brand' => $this->brand,
            'category' => $this->category,
            'gallery' => $this->gallery,
            'attributes' => array_map(fn($attrSet) => $attrSet->toArray(), $this->attributeSets),
            'prices' => array_map(fn($price) => $price->toArray(), $this->prices),
        ];
    }

    public static function createFromData(array $data): self
    {
        return match ($data['category_name'] ?? 'default') {
            'clothes' => new ClothesProduct($data),
            'tech' => new TechProduct($data),
            default => new DefaultProduct($data),
        };
    }
}
