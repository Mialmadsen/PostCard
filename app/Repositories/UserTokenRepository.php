<?php

declare(strict_types=1);

namespace PostCard\Repositories;

// All SQL for the user_tokens table: one-time links for e-mail verification and password reset.
class UserTokenRepository extends Repository
{
    public function create(int $userId, string $tokenHash, string $type, int $minutes): int
    {
        return $this->insert(
            'INSERT INTO user_tokens (user_id, token_hash, token_type, expires_at)
             VALUES (:user_id, :token_hash, :token_type, DATE_ADD(NOW(), INTERVAL :minutes MINUTE))',
            [
                'user_id' => $userId,
                'token_hash' => $tokenHash,
                'token_type' => $type,
                'minutes' => $minutes,
            ]
        );
    }

    public function findValid(string $tokenHash, string $type): ?array
    {
        return $this->fetchOne(
            'SELECT token_id, user_id
               FROM user_tokens
              WHERE token_hash = :token_hash
                AND token_type = :token_type
                AND used_at IS NULL
                AND expires_at > NOW()',
            ['token_hash' => $tokenHash, 'token_type' => $type]
        );
    }


    public function markUsed(int $tokenId): int
    {
        return $this->execute(
            'UPDATE user_tokens
                SET used_at = NOW()
              WHERE token_id = :token_id
                AND used_at IS NULL',
            ['token_id' => $tokenId]
        );
    }

    // How many unused, unexpired links of this type the user has right now
    public function countValid(int $userId, string $type): int
    {
        $count = $this->fetchValue(
            'SELECT COUNT(*)
               FROM user_tokens
              WHERE user_id = :user_id
                AND token_type = :token_type
                AND used_at IS NULL
                AND expires_at > NOW()',
            ['user_id' => $userId, 'token_type' => $type]
        );

        return (int) $count;
    }

    // Ugyldiggør tidligere ubrugte tokens af en bestemt type for brugeren.
    public function invalidatePrevious(int $userId, string $type): int
    {
        return $this->execute(
            'UPDATE user_tokens
                SET used_at = NOW()
              WHERE user_id = :user_id
                AND token_type = :token_type
                AND used_at IS NULL',
            [
                'user_id' => $userId,
                'token_type' => $type,
            ]
        );
    }
}