<?php

namespace App\GraphQL\Resolvers;

use App\Database\Connection;
use App\Models\Category;
use PDO;

class CategoryResolver
{
    public function getCategories(): array
    {
        $pdo = Connection::getConnection();

        $stmt = $pdo->query("SELECT id, name FROM categories");
        $categoriesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            fn(array $catData) => Category::createFromData($catData)->toArray(),
            $categoriesData
        );
    }
}
