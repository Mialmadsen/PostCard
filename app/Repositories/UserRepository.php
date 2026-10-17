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

    // True only for an active platform operator (is_operator = 1).
    public function isOperator(int $userId): bool
    {
        $value = $this->fetchValue(
            'SELECT is_operator FROM users WHERE user_id = :user_id AND user_status = \'active\'',
            ['user_id' => $userId]
        );

        return $value === 1;
    }
}
