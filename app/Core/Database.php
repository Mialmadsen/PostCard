<?php

declare(strict_types=1);

// The namespace is like a surname for the class
namespace PostCard\Core;

class Database
{
    // Type PDO (PHP Data Object). There is no connection at first, so it is null
    private static ?\PDO $pdo = null;

    public static function connection(): \PDO
    {
        if (self::$pdo === null) {
            $host = Config::get('DB_HOST');
            $port = Config::get('DB_PORT');
            $databaseName = Config::get('DB_NAME');
            $user = Config::get('DB_USER');
            $password = Config::get('DB_PASS');

            // DSN (data source name): one string with the connection details in a fixed order
            $dsn = "mysql:host=$host;port=$port;dbname=$databaseName;charset=utf8mb4";

            // We create the PDO once from the DSN, user and password. Every later request uses the same connection (the coffee machine metaphor)
            self::$pdo = new \PDO($dsn, $user, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]);

            // Strict mode: MySQL refuses missing or wrong values instead of guessing
            self::$pdo->exec("SET SESSION sql_mode = 'STRICT_ALL_TABLES,NO_ZERO_DATE,NO_ZERO_IN_DATE,ERROR_FOR_DIVISION_BY_ZERO'");

            $offset = date('P');
            self::$pdo->exec("SET time_zone = '$offset'");
        }

        return self::$pdo;
    }
}
