<?php

namespace App\GraphQL\Resolvers;

use App\Database\Connection;
use App\Models\Category;
use PDO;

class CategoryResolver {
    public function getCategories() {
        $pdo = Connection::getConnection();
        $stmt = $pdo->prepare("SELECT id, name FROM categories");
        $stmt->execute();
        $categoriesData = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $categories = [];
        foreach ($categoriesData as $catData) {
            $categories[] = Category::createFromData($catData);
        }
        // Return arrays for GraphQL
        return array_map(fn(Category $cat) => $cat->toArray(), $categories);
    }
}
