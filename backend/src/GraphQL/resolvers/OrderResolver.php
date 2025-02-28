<?php

namespace App\GraphQL\Resolvers;

use App\Database\Connection;
use Exception;

class OrderResolver {
    /**
     * Creates a new order in the database.
     * Expected input: items [{ productId, quantity, chosenAttributes }]
     * @throws Exception
     */
    public function createOrder($args): array
    {
        $pdo = Connection::getConnection();
        $items = $args['items'];
        try {
            $pdo->beginTransaction();
            // Create order
            $stmtOrder = $pdo->prepare("INSERT INTO orders () VALUES ()");
            $stmtOrder->execute();
            $orderId = $pdo->lastInsertId();

            $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, chosen_attributes) VALUES (:order_id, :product_id, :quantity, :chosen_attributes)");
            foreach ($items as $item) {
                $chosenAttributesJson = json_encode($item['chosenAttributes'] ?? []);
                $stmtItem->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $item['productId'],
                    ':quantity' => $item['quantity'],
                    ':chosen_attributes' => $chosenAttributesJson,
                ]);
            }
            $pdo->commit();

            // Return the created order as an array for GraphQL
            return [
                'id' => (int)$orderId,
                'items' => array_map(function($item) {
                    return [
                        'productId' => $item['productId'],
                        'quantity' => $item['quantity'],
                        'chosenAttributes' => $item['chosenAttributes'] ?? []
                    ];
                }, $items)
            ];
        } catch (Exception $e) {
            $pdo->rollBack();
            throw new Exception("Failed to create order: " . $e->getMessage());
        }
    }
}
