<?php
namespace App\Controller;

use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Utils\BuildSchema;
use RuntimeException;
use Throwable;
use App\GraphQL\Resolvers\CategoryResolver;
use App\GraphQL\Resolvers\ProductResolver;
use App\GraphQL\Resolvers\OrderResolver;

class GraphQL {
    static public function handle() {
        try {
            $schemaPath = __DIR__ . '/../GraphQL/schema.graphql';
            if (!file_exists($schemaPath)) {
                throw new RuntimeException("Schema file not found at: " . $schemaPath);
            }
            $schemaContent = file_get_contents($schemaPath);
            $schema = BuildSchema::build($schemaContent);

            $rawInput = file_get_contents('php://input');
            if ($rawInput === false) {
                throw new RuntimeException('Failed to get php://input');
            }
            $input = json_decode($rawInput, true);
            $query = $input['query'];
            $variableValues = $input['variables'] ?? null;

            $rootValue = [
                'categories' => function() {
                    $resolver = new CategoryResolver();
                    return $resolver->getCategories();
                },
                'products' => function($root, $args) {
                    $resolver = new ProductResolver();
                    return $resolver->getProducts($root, $args);
                },
                'product' => function($root, $args) {
                    $resolver = new ProductResolver();
                    return $resolver->getProduct($root, $args);
                },
                'createOrder' => function($root, $args) {
                    $resolver = new OrderResolver();
                    return $resolver->createOrder($root, $args);
                }
            ];

            $result = GraphQLBase::executeQuery($schema, $query, $rootValue, null, $variableValues);
            $output = $result->toArray();
        } catch (Throwable $e) {
            $output = [
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }

        header('Content-Type: application/json; charset=UTF-8');
        return json_encode($output);
    }
}
