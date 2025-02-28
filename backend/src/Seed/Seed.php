<?php

namespace App\Seed;

use App\Database\Connection;

class Seed {
    public static function run() {
        $pdo = Connection::getConnection();

        // Load the JSON file from the project root (adjust the path if needed)
        $jsonFile = __DIR__ . '/data.json';
        if (!file_exists($jsonFile)) {
            die("data.json file not found at " . $jsonFile);
        }
        $json = file_get_contents($jsonFile);
        $data = json_decode($json, true)['data'];

        // 1. Insert Categories
        $catStmt = $pdo->prepare("INSERT INTO categories (name) VALUES (:name)");
        foreach ($data['categories'] as $cat) {
            $catStmt->execute([':name' => $cat['name']]);
        }
        // Build a mapping from category name to its DB id
        $catMap = [];
        $stmt = $pdo->query("SELECT id, name FROM categories");
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $catMap[$row['name']] = $row['id'];
        }

        // 2. Prepare statements for Products and related tables
        $productStmt = $pdo->prepare("
            INSERT INTO products (id, name, in_stock, description, brand, category_id)
            VALUES (:id, :name, :in_stock, :description, :brand, :category_id)
        ");

        $galleryStmt = $pdo->prepare("
            INSERT INTO product_gallery (product_id, image_url) 
            VALUES (:product_id, :image_url)
        ");

        $attrSetStmt = $pdo->prepare("
            INSERT INTO attribute_sets (product_id, name, type) 
            VALUES (:product_id, :name, :type)
        ");

        $attrStmt = $pdo->prepare("
            INSERT INTO attributes (attribute_set_id, display_value, value)
            VALUES (:attribute_set_id, :display_value, :value)
        ");

        $priceStmt = $pdo->prepare("
            INSERT INTO prices (product_id, amount, currency_label, currency_symbol)
            VALUES (:product_id, :amount, :currency_label, :currency_symbol)
        ");

        // 3. Insert Products and their related data
        foreach ($data['products'] as $product) {
            $inStock = $product['inStock'] ? 1 : 0;
            // Lookup the category id using the category name provided in the product
            $categoryId = $catMap[$product['category']] ?? null;

            $productStmt->execute([
                ':id' => $product['id'],
                ':name' => $product['name'],
                ':in_stock' => $inStock,
                ':description' => $product['description'],
                ':brand' => $product['brand'],
                ':category_id' => $categoryId,
            ]);

            // Insert gallery images
            foreach ($product['gallery'] as $imgUrl) {
                $galleryStmt->execute([
                    ':product_id' => $product['id'],
                    ':image_url' => $imgUrl,
                ]);
            }

            // Insert attribute sets and then their attributes
            foreach ($product['attributes'] as $attrSet) {
                // Use the "id" field from data.json as the attribute set name
                $attrSetStmt->execute([
                    ':product_id' => $product['id'],
                    ':name' => $attrSet['id'],
                    ':type' => $attrSet['type']
                ]);
                $attributeSetId = $pdo->lastInsertId();

                foreach ($attrSet['items'] as $attr) {
                    $attrStmt->execute([
                        ':attribute_set_id' => $attributeSetId,
                        ':display_value' => $attr['displayValue'],
                        ':value' => $attr['value']
                    ]);
                }
            }

            // Insert prices for the product
            foreach ($product['prices'] as $price) {
                $priceStmt->execute([
                    ':product_id' => $product['id'],
                    ':amount' => $price['amount'],
                    ':currency_label' => $price['currency']['label'],
                    ':currency_symbol' => $price['currency']['symbol']
                ]);
            }
        }

        echo "Seeding complete.\n";
    }
}
