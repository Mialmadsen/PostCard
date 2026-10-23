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

    // Henter en brugers fornavn ud fra deres ID
    public function findFirstNameById(int $userId): ?string
    {
        $firstName = $this->fetchValue(
            'SELECT first_name FROM users WHERE user_id = :user_id',
            ['user_id' => $userId]
        );

        return $firstName !== null ? (string) $firstName : null;
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

    // True if any user (also blocked or deleted) already has this email.
    public function emailExists(string $email): bool
    {
        $value = $this->fetchValue(
            'SELECT 1 FROM users WHERE email = :email LIMIT 1',
            ['email' => $email]
        );

        return $value !== null;
    }

    // True if any user (also blocked or deleted) already has this username.
    public function usernameExists(string $username): bool
    {
        $value = $this->fetchValue(
            'SELECT 1 FROM users WHERE username = :username LIMIT 1',
            ['username' => $username]
        );

        return $value !== null;
    }

    // We create a new user in db and return users id
    public function create(
        string $firstName,
        string $lastName,
        string $username,
        string $email,
        string $birthdate,
        string $passwordHash
    ): int {
        return $this->insert(
            'INSERT INTO users (first_name, last_name, username, email, birthdate, password_hash)
             VALUES (:first_name, :last_name, :username, :email, :birthdate, :password_hash)',
            [
                'first_name' => $firstName,
                'last_name' => $lastName,
                'username' => $username,
                'email' => $email,
                'birthdate' => $birthdate,
                'password_hash' => $passwordHash
            ]
        );
    }
}