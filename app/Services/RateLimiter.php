<?php

declare(strict_types=1);

namespace PostCard\Services;

use PostCard\Core\Database;

// One place for all "not too often" rules: how many times a user may do an action in a period.
class RateLimiter
{
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
}
