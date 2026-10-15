<?php

declare(strict_types=1);

namespace PostCard\Repositories;

use PostCard\Core\Database;
use PDO;

// All SQL for the users table.
class UserRepository
{
    // The shared database connection, fetched once when the repository is created.
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::connection();
    }

    public function findByEmail(string $email): ?array
    {
        $sql = 'SELECT user_id, password_hash, user_status FROM users WHERE email=:email';
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['email' => $email]);
        $user = $statement->fetch();
        return ($user === false ? null : $user);
    }
}
