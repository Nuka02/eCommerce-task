<?php
namespace App\Models;

abstract class Product {
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

    /**
     * A helper to initialize common properties.
     */
    protected function initialize(array $data): void {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->inStock = (bool)$data['in_stock'];
        $this->description = $data['description'];
        $this->brand = $data['brand'];
        $this->category = $data['category'];
        $this->gallery = $data['gallery'];
        $this->attributeSets = array_map(fn($attrSet) => AttributeSet::createFromData($attrSet), $data['attributes']);
        $this->prices = array_map(fn($priceData) => new Price($priceData), $data['prices']);
    }

    /**
     * Concrete classes must implement their constructor.
     */
    abstract public function __construct(array $data);

    /**
     * Convert the Product to an array for GraphQL consumption.
     */
    public function toArray(): array {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'inStock'      => $this->inStock,
            'description'  => $this->description,
            'brand'        => $this->brand,
            'category'     => $this->category,
            'gallery'      => $this->gallery,
            'attributes'   => array_map(fn($attrSet) => $attrSet->toArray(), $this->attributeSets),
            'prices'       => array_map(fn($price) => $price->toArray(), $this->prices),
        ];
    }

    /**
     * Factory method to create a Product instance.
     */
    public static function createFromData(array $data): self {
        if (isset($data['category_name'])) {
            $data['category'] = $data['category_name'];
        }
        if ($data['category'] === 'clothes') {
            return new ClothesProduct($data);
        } elseif ($data['category'] === 'tech') {
            return new TechProduct($data);
        } else {
            return new DefaultProduct($data);
        }
    }
}
