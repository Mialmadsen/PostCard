<?php

declare(strict_types=1);

namespace PostCard\Core;

// Owns the session: starting it safely, renewing the id at login, destroying it at logout.
class Session
{
    // Start the session with a safe cookie (the "wristband").
    public static function start(): void
    {
        // Is this request HTTPS? The key only exists on HTTPS, and some servers set it to 'off'.
        $isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

        $cookieParams = [
            'lifetime' => 0,                 // gone when the browser closes
            'path' => BASE_PATH . '/',       // only sent to this app
            'secure' => $isHttps,            // only over HTTPS (true on the host, false on WAMP)
            'httponly' => true,              // JavaScript can't read it, so XSS can't steal it
            'samesite' => 'Lax',             // not sent with forms posted from other websites
        ];

        session_set_cookie_params($cookieParams);
        session_start();
    }

    // Give the visitor a new session id (at login), so an old or stolen id becomes useless.
    public static function regenerate(): void
    {
        session_regenerate_id(true);
    }

    // Log out completely: empty the session, delete its cookie, destroy it on the server.
    public static function destroy(): void
    {
        // empty the locker
        $_SESSION = [];

        // delete cookie
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);

        // Destroy the session
        session_destroy();
    }
}
