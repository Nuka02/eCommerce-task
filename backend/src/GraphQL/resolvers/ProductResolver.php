<?php

namespace App\GraphQL\Resolvers;

use App\Database\Connection;
use App\Models\Product;
use PDO;

class ProductResolver {
    public function getProducts($root,$args) {
        $pdo = Connection::getConnection();

        // Filter by category if specified
        if (isset($args['category']) && strtolower($args['category']) !== 'all') {
            $stmt = $pdo->prepare(
                "SELECT p.*, c.name as category_name 
                 FROM products p 
                 LEFT JOIN categories c ON p.category_id = c.id 
                 WHERE c.name = :name"
            );
            $stmt->execute([':name' => $args['category']]);
        } else {
            $stmt = $pdo->query(
                "SELECT p.*, c.name as category_name 
                 FROM products p 
                 LEFT JOIN categories c ON p.category_id = c.id"
            );
        }

        $productsData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $products = [];
        foreach ($productsData as $pData) {
            $pData['gallery'] = $this->getGallery($pData['id']);
            $pData['attributes'] = $this->getAttributes($pData['id']);
            $pData['prices'] = $this->getPrices($pData['id']);
            // Create Product instance based on its category
            $products[] = Product::createFromData($pData);
        }
        return array_map(fn($product) => $product->toArray(), $products);
    }

    public function getProduct($root, $args): ?array
    {
        $pdo = Connection::getConnection();
        $stmt = $pdo->prepare(
            "SELECT p.*, c.name as category_name 
             FROM products p 
             LEFT JOIN categories c ON p.category_id = c.id 
             WHERE p.id = :id"
        );
        $stmt->execute([':id' => $args['id']]);
        $pData = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$pData) {
            return null;
        }
        $pData['gallery'] = $this->getGallery($pData['id']);
        $pData['attributes'] = $this->getAttributes($pData['id']);
        $pData['prices'] = $this->getPrices($pData['id']);
        $product = Product::createFromData($pData);
        return $product->toArray();
    }

    private function getGallery(string $productId): array {
        $pdo = Connection::getConnection();
        $stmt = $pdo->prepare("SELECT image_url FROM product_gallery WHERE product_id = :id");
        $stmt->execute([':id' => $productId]);
        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'image_url');
    }

    private function getAttributes(string $productId): array {
        $pdo = Connection::getConnection();
        $stmt = $pdo->prepare("SELECT id, name, type FROM attribute_sets WHERE product_id = :id");
        $stmt->execute([':id' => $productId]);
        $rawSets = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rawSets as &$set) {
            $stmt2 = $pdo->prepare("SELECT display_value, value, id FROM attributes WHERE attribute_set_id = :setId");
            $stmt2->execute([':setId' => $set['id']]);
            $set['items'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        }

        //conversion to models handled in Product::createFromData()
        return $rawSets;
    }


    private function getPrices(string $productId): array {
        $pdo = Connection::getConnection();
        $stmt = $pdo->prepare("SELECT amount, currency_label, currency_symbol FROM prices WHERE product_id = :id");
        $stmt->execute([':id' => $productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
