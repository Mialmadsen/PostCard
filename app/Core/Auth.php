<?php

declare(strict_types=1);

namespace PostCard\Core;

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
}
