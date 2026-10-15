<?php

declare(strict_types=1);

namespace PostCard\Repositories;

use PostCard\Core\Database;

// All SQL for the users table.
class UserRepository
{
    public function findByEmail(string $email): ?array
    {
        $sql = 'SELECT user_id, password_hash, user_status FROM users WHERE email=:email';
        $statement = Database::connection()->prepare($sql);
        $statement->execute(['email' => $email]);
        $user = $statement->fetch();
        return ($user === false ? null : $user);
    }
}
