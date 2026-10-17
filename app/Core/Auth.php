<?php

declare(strict_types=1);

namespace PostCard\Core;

use PostCard\Repositories\GroupMemberRepository;
use PostCard\Repositories\UserRepository;

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

    // Log this user in: a new session id, then remember who it is.
    public static function login(int $userId): void
    {
        Session::regenerate();
        $_SESSION['user_id'] = $userId;
    }

    // Log out whoever is logged in: the session is destroyed completely.
    public static function logout(): void
    {
        Session::destroy();
    }

    // Checks if you have access to the group
    public static function requireMember(int $groupId): void
    {
        $role = self::roleIn($groupId);
        if ($role === null) {
            http_response_code(403);
            echo 'Du har ikke adgang til denne gruppe.';
            exit;
        }
    }

    // Stop here with 403 unless the user is admin of this group.
    public static function requireGroupAdmin(int $groupId): void
    {
        $role = self::roleIn($groupId);
        if ($role !== 'group_admin') {
            http_response_code(403);
            echo 'Kun gruppens administrator har adgang til denne side.';
            exit;
        }
    }

    // Stop here with 403 unless the user is an active platform operator.
    public static function requireOperator(): void
    {
        self::requireLogin();
        $users = new UserRepository();
        $isOperator = $users->isOperator(self::userId());
        if ($isOperator === false) {
            http_response_code(403);
            echo 'Kun operatører har adgang til denne side.';
            exit;
        }
    }

    // Check the role in the group
    private static function roleIn(int $groupId): ?string
    {
        self::requireLogin();
        $members = new GroupMemberRepository();
        $role = $members->findActiveRole(self::userId(), $groupId);
        return $role;
    }
}
