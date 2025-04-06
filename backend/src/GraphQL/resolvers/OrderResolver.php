<?php

namespace App\GraphQL\Resolvers;

use App\Database\Connection;
use Exception;
use PDO;

class OrderResolver
{
    /**
     * Creates a new order in the database.
     * Expected input: items [{ productId, quantity, chosenAttributes }]
     *
     * @param mixed $root
     * @param array $args
     * @return array
     * @throws Exception
     */
    public function createOrder($root, array $args): array
    {
        $pdo = Connection::getConnection();
        $items = $args['items'] ?? [];

        if (empty($items)) {
            throw new Exception('No items provided for the order.');
        }

        try {
            $pdo->beginTransaction();

            // Insert order (with current timestamp)
            $stmtOrder = $pdo->prepare("INSERT INTO orders (created_at) VALUES (NOW())");
            $stmtOrder->execute();
            $orderId = (int) $pdo->lastInsertId();

            $stmtItem = $pdo->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, chosen_attributes)
                VALUES (:order_id, :product_id, :quantity, :chosen_attributes)
            ");

            foreach ($items as $item) {
                $stmtItem->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $item['productId'],
                    ':quantity' => $item['quantity'],
                    ':chosen_attributes' => json_encode($item['chosenAttributes'] ?? []),
                ]);
            }

            $pdo->commit();

            return [
                'id' => $orderId,
                'items' => array_map(fn($item) => [
                    'productId' => $item['productId'],
                    'quantity' => $item['quantity'],
                    'chosenAttributes' => $item['chosenAttributes'] ?? [],
                ], $items),
            ];
        } catch (Exception $e) {
            $pdo->rollBack();
            throw new Exception("Failed to create order: " . $e->getMessage(), 0, $e);
        }
    }
}
