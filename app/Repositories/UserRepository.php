<?php

declare(strict_types=1);

namespace PostCard\Repositories;

// All SQL for the users table.
class UserRepository extends Repository
{
    public function findByEmail(string $email): ?array
    {
        return $this->fetchOne(
            'SELECT user_id, password_hash, user_status FROM users WHERE email = :email',
            ['email' => $email]
        );
    }
}
