<?php

namespace App\GraphQL\Resolvers;

use App\Database\Connection;
use App\Models\Product;
use PDO;

class ProductResolver
{
    public function getProducts($root, $args): array
    {
        $pdo = Connection::getConnection();

        $query = "
            SELECT p.*, c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
        ";

        $params = [];

        if (!empty($args['category']) && strtolower($args['category']) !== 'all') {
            $query .= " WHERE c.name = :name";
            $params[':name'] = $args['category'];
        }

        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        $productsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Enrich each product data with gallery, attributes, prices
        foreach ($productsData as &$pData) {
            $pData['gallery'] = $this->getGallery($pData['id']);
            $pData['attributes'] = $this->getAttributes($pData['id']);
            $pData['prices'] = $this->getPrices($pData['id']);
        }

        // Transform data into Product models
        return array_map(fn($productData) => Product::createFromData($productData)->toArray(), $productsData);
    }

    public function getProduct($root, $args): ?array
    {
        $pdo = Connection::getConnection();

        $stmt = $pdo->prepare("
            SELECT p.*, c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE p.id = :id
        ");
        $stmt->execute([':id' => $args['id']]);

        $pData = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pData) {
            return null;
        }

        $pData['gallery'] = $this->getGallery($pData['id']);
        $pData['attributes'] = $this->getAttributes($pData['id']);
        $pData['prices'] = $this->getPrices($pData['id']);

        return Product::createFromData($pData)->toArray();
    }

    private function getGallery(string $productId): array
    {
        $pdo = Connection::getConnection();
        $stmt = $pdo->prepare("SELECT image_url FROM product_gallery WHERE product_id = :id");
        $stmt->execute([':id' => $productId]);

        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'image_url');
    }

    private function getAttributes(string $productId): array
    {
        $pdo = Connection::getConnection();

        $stmt = $pdo->prepare("SELECT id, name, type FROM attribute_sets WHERE product_id = :id");
        $stmt->execute([':id' => $productId]);
        $attributeSets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($attributeSets as &$set) {
            $stmtItems = $pdo->prepare("SELECT display_value, value, id FROM attributes WHERE attribute_set_id = :setId");
            $stmtItems->execute([':setId' => $set['id']]);
            $set['items'] = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
        }

        return $attributeSets;
    }

    private function getPrices(string $productId): array
    {
        $pdo = Connection::getConnection();
        $stmt = $pdo->prepare("SELECT amount, currency_label, currency_symbol FROM prices WHERE product_id = :id");
        $stmt->execute([':id' => $productId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
