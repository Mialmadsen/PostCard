<?php

declare(strict_types=1);

namespace PostCard\Repositories;

// All SQL for the login_attempts table.
class LoginAttemptRepository extends Repository
{
    public function countRecent(string $email): int
    {
        return (int) $this->fetchValue(
            'SELECT COUNT(*)
               FROM login_attempts
              WHERE email = :email
                AND attempted_at > NOW() - INTERVAL 15 MINUTE',
            ['email' => $email]
        );
    }

    public function add(string $email): void
    {
        $this->execute(
            'INSERT INTO login_attempts (email) VALUES (:email)',
            ['email' => $email]
        );
    }


    public function clear(string $email): void
    {
        $this->execute(
            'DELETE FROM login_attempts WHERE email = :email',
            ['email' => $email]
        );
    }
}
