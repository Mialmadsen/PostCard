<?php

declare(strict_types=1);

namespace PostCard\Services;

use PostCard\Core\Database;

// One place for all "not too often" rules: how many times a user may do an action in a period.
class RateLimiter

{
    // action => [max, minutes]. Limits public requests based on the user's e-mail address.
    private const PUBLIC_LIMITS = [
        'reset_password' => [
            'max' => 5,
            'minutes' => 15,
        ],
    ];

    // action => [max, minutes,message, query]. The query must count this user's rows in the period.
    private const LIMITS = [
        'create_group' => [
            'max' => 5,
            'minutes' => 24 * 60,
            'message' => 'Du kan højst oprette 5 grupper om dagen. Prøv igen i morgen.',
            'sql' => "SELECT COUNT(*)
                        FROM group_members
                       WHERE user_id = :user_id
                         AND member_role = 'group_admin'
                         AND joined_at > NOW() - INTERVAL :minutes MINUTE",
        ],
        'post_comment' => [
            'max' => 30,
            'minutes' => 60,
            'message' => 'Du kan højst oprette 30 kommentarer om timen. Prøv igen om en time.',
            'sql' => 'SELECT COUNT(*)
                        FROM comments
                       WHERE user_id = :user_id
                         AND created_at > NOW() - INTERVAL :minutes MINUTE',
        ],
        'create_invite' => [
            'max' => 20,
            'minutes' => 24 * 60,
            'message' => 'Du kan højst lave 20 invitationslinks om dagen. Prøv igen i morgen.',
            'sql' => 'SELECT COUNT(*)
                        FROM invitations
                       WHERE created_by = :user_id
                         AND created_at > NOW() - INTERVAL :minutes MINUTE',
        ],

        'create_post' => [
            'max' => 30,
            'minutes' => 60,
            'message' => 'Du kan højst oprette 30 opslag i timen. Prøv igen om en time.',
            'sql' => 'SELECT COUNT(*)
                        FROM posts AS p
                        JOIN experiences AS e ON p.experience_id = e.experience_id
                       WHERE e.user_id = :user_id
                         AND p.created_at > NOW() - INTERVAL :minutes MINUTE',
        ],
        'create_experience' => [
        'max' => 10,
        'minutes' => 24 * 60,
        'message' => 'Du kan højst oprette 10 oplevelser på 24 timer. Prøv igen senere.',
        'sql' => 'SELECT COUNT(*)
                    FROM experiences
                WHERE user_id = :user_id
                    AND created_at > NOW() - INTERVAL :minutes MINUTE',
        ],

        'create_experience_burst' => [
            'max' => 1,
            'minutes' => 1,
            'message' => 'Vent mindst ét minut, før du opretter en ny oplevelse.',
            'sql' => 'SELECT COUNT(*)
                        FROM experiences
                    WHERE user_id = :user_id
                        AND created_at > NOW() - INTERVAL :minutes MINUTE',
        ],

        'reset_password' => [
            'max' => 3,
            'minutes' => 60,
            'message' => 'Der er anmodet om for mange links. Prøv igen senere.',
            'sql' => "SELECT COUNT(*)
                        FROM user_tokens
                    WHERE user_id = :user_id
                        AND token_type = 'reset_password'
                        AND DATE_SUB(expires_at, INTERVAL 60 MINUTE)
                            > NOW() - INTERVAL :minutes MINUTE",
        ],

    ];

    // True when the user has already done this action as often as allowed in the period.
    public function tooMany(string $action, int $userId): bool
    {
        $limit = self::LIMITS[$action] ?? null;
        if ($limit === null) {
            // A typo in the action name must fail loudly, never silently allow everything.
            throw new \InvalidArgumentException('Unknown rate limit action: ' . $action);
        }

        return $this->count($limit['sql'], $userId, $limit['minutes']) >= $limit['max'];
    }

    // Runs one action's COUNT(*) query and returns the number.
    private function count(string $sql, int $userId, int $minutes): int
    {
        $statement = Database::connection()->prepare($sql);
        $statement->execute([
            'user_id' => $userId,
            'minutes' => $minutes,
        ]);

        return (int) $statement->fetchColumn();
    }

    // The Danish text the form shows when tooMany() is true.
    public function message(string $action): string
    {
        return self::LIMITS[$action]['message'];
    }

    // Registrerer og begrænser offentlige anmodninger ud fra e-mailadressen.
    public function tooManyPublic(string $action, string $email): bool
    {
        $limit = self::PUBLIC_LIMITS[$action] ?? null;

        if ($limit === null) {
            throw new \InvalidArgumentException(
                'Unknown public rate limit action: ' . $action
            );
        }

        $email = mb_strtolower(trim($email));

        if (strlen($email) > 255 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return true;
        }

        // Rydder op i gamle registreringer.
        $this->clearOldPublicAttempts();

        $db = Database::connection();

        // Tæller anmodninger inden for den angivne periode.
        $statement = $db->prepare(
            'SELECT COUNT(*)
               FROM public_rate_limits
              WHERE action_name = :action
                AND email = :email
                AND attempted_at > NOW() - INTERVAL :minutes MINUTE'
        );

        $statement->execute([
            'action' => $action,
            'email' => $email,
            'minutes' => $limit['minutes'],
        ]);

        // Stopper anmodningen, hvis grænsen er nået.
        if ((int) $statement->fetchColumn() >= $limit['max']) {
            return true;
        }

        // Registrerer den nye anmodning.
        $statement = $db->prepare(
            'INSERT INTO public_rate_limits (action_name, email)
             VALUES (:action, :email)'
        );

        $statement->execute([
            'action' => $action,
            'email' => $email,
        ]);

        return false;
    }

    // Sletter gamle registreringer af offentlige anmodninger.
    private function clearOldPublicAttempts(): void
    {
        $statement = Database::connection()->prepare(
            'DELETE FROM public_rate_limits
            WHERE attempted_at < NOW() - INTERVAL 1 DAY'
        );

        $statement->execute();
    }
}