<?php

declare(strict_types=1);

namespace PostCard\Core;

use PostCard\Repositories\GroupMemberRepository;

// The door guard: who is logged in, and may they be here?
class Auth
{
    public static function userId(): ?int
    {
        return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
    }

    // Stop here and go to the login page if nobody is logged in.
    public static function requireLogin(): void
    {
        if (self::userId() === null) {
            header('Location: ' . BASE_PATH . '/login');
            exit;
        }
    }

    // Checks if you have access to the group
    public static function requireMember(int $groupId): void
    {
        self::requireLogin();
        $members = new GroupMemberRepository();
        $role = $members->findActiveRole(self::userId(), $groupId);
        if ($role === null) {
            http_response_code(403);
            echo 'Du har ikke adgang til denne gruppe.';
            exit;
        }
    }
}
