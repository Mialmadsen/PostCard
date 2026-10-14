<?php

declare(strict_types=1);
// it is like a surname to the class
namespace PostCard\Core;

class Database
{ //type of data:PHP Data Object, there is no connection first, so it is null
    private static ?\PDO $pdo = null;
    public static function connection(): \PDO
    {
        if (self::$pdo === null) {
            $config = require __DIR__ . '/../../config/config.php'; //here we take array with credencials from config.php
            $dsn = "mysql:host={$config['DB_HOST']};port={$config['DB_PORT']};dbname={$config['DB_NAME']};charset=utf8mb4";
            // data source name , it is a string with information in specific order
            //we create a new pdo with three parameters : dsn, username and password and return it , so that it is used in all other requests to connect, coffee machine metaphor
            self::$pdo = new \PDO($dsn, $config['DB_USER'], $config['DB_PASS'], [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        return self::$pdo;
    }
}
