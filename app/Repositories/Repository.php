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

    // Run a query and return all rows (an empty array if none).
    protected function fetchAll(string $sql, array $params = []): array
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    // Run a query and return one value (the first column of the first row), or null if nothing was found.
    protected function fetchValue(string $sql, array $params = []): mixed
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);
        $value = $statement->fetchColumn();
        return ($value === false ? null : $value);
    }

    // Run an INSERT and return the id MySQL created for the new row (AUTO_INCREMENT).
    protected function insert(string $sql, array $params = []): int
    {
        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        // The connection, not the statement, remembers the last new id. It comes as text, so make it an int.
        return (int) $this->pdo->lastInsertId();
    }
}
