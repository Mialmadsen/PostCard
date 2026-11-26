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

    // Opretter et reset-token, hvis brugeren ikke allerede har et gyldigt.
    public function createPasswordReset(
        int $userId,
        string $email,
        string $tokenHash,
        int $minutes
    ): ?int {
        return $this->transaction(function () use (
            $userId,
            $email,
            $tokenHash,
            $minutes
        ): ?int {

            // Kontrollerer og låser brugeren under transaktionen.
            $user = $this->fetchOne(
                "SELECT user_id
                FROM users
                WHERE user_id = :user_id
                    AND email = :email
                    AND user_status = 'active'
                    AND email_verified_at IS NOT NULL
                FOR UPDATE",
                [
                    'user_id' => $userId,
                    'email' => $email,
                ]
            );

            if ($user === null) {
                return null;
            }

            // Begrænser antallet af reset-links pr. bruger.
            $rateLimiter = new \PostCard\Services\RateLimiter();

            if ($rateLimiter->tooMany('reset_password', $userId)) {
                return null;
            }

            // Kontrollerer, om brugeren allerede har et gyldigt link.
            if ($this->countValid($userId, 'reset_password') > 0) {
                return null;
            }

            // Genbruger den eksisterende metode til oprettelse af tokens.
            return $this->create(
                $userId,
                $tokenHash,
                'reset_password',
                $minutes
            );
        });
    }

    // Opdaterer adgangskoden og ugyldiggør reset-tokens i samme transaktion.
    public function resetPassword(string $tokenHash, string $passwordHash): bool
    {
        return $this->transaction(function () use ($tokenHash, $passwordHash): bool {

            // Finder det gyldige reset-token.
            $token = $this->findValid($tokenHash, 'reset_password');

            if ($token === null) {
                return false;
            }

            $userId = (int) $token['user_id'];

            // Kontrollerer og låser brugeren under transaktionen.
            $user = $this->fetchOne(
                "SELECT user_id
                FROM users
                WHERE user_id = :user_id
                AND user_status = 'active'
                AND email_verified_at IS NOT NULL
                FOR UPDATE",
                ['user_id' => $userId]
            );

            if ($user === null) {
                return false;
            }

            // Markerer tokenet som brugt, hvis det stadig er gyldigt.
            $changed = $this->execute(
                "UPDATE user_tokens
                SET used_at = NOW()
                WHERE token_id = :token_id
                AND token_type = 'reset_password'
                AND used_at IS NULL
                AND expires_at > NOW()",
                ['token_id' => (int) $token['token_id']]
            );

            if ($changed !== 1) {
                return false;
            }

            // Gemmer den nye hashede adgangskode.
            $users = new UserRepository();
            $users->updatePassword($userId, $passwordHash);

            return true;
        });
    }
}