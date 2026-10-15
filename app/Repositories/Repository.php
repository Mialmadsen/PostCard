<?php

declare(strict_types=1);

namespace PostCard\Repositories;

use PostCard\Core\Database;
use PDO;

// Parent of all repositories: the shared connection and the query helpers.
abstract class Repository
{
    // The shared database connection, fetched once when a repository is created.
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connection();
    }

    // Run a query and return one row, or null if nothing was found.
    protected function fetchOne(string $sql, array $params = []): ?array
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        $row = $statement->fetch();
        return ($row === false ? null : $row);
    }

    protected function fetchAll(string $sql, array $params = []): array
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    protected function fetchValue(string $sql, array $params = []): mixed
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        $value = $statement->fetchColumn();
        return ($value === false ? null : $value);
    }
}
