<?php

namespace App\Database;

use PDO;
use PDOException;
use Dotenv;

class Connection
{
    private static ?PDO $pdo = null;

    public static function getConnection(): PDO
    {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../../');
        $dotenv->load();
        if (!self::$pdo) {
            $host = $_ENV['DB_HOST'];
            $db   = $_ENV['DB_NAME'];
            $user = $_ENV['DB_USER'];
            $pass = $_ENV['DB_PASS'];
            $dsn  = "mysql:host=$host;dbname=$db;charset=utf8";

            try {
                self::$pdo = new PDO($dsn, $user, $pass, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]);
            } catch (PDOException $e) {
                die("DB Connection failed: " . $e->getMessage());
            }
        }

        return self::$pdo;
    }
}
