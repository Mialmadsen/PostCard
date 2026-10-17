<?php

declare(strict_types=1);

namespace PostCard\Core;

// Protection against forged forms: a secret token in every form, checked on every POST.
class Csrf
{
    // The session's secret token, created the first time it's needed (one per session).
    public static function token(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    // The hidden input every POST form needs (print it with \PostCard\Core\Csrf::field() in a view).
    public static function field(): string
    {
        return '<input type="hidden" name="csrf_token" value="' . e(self::token()) . '">';
    }

    // Stop with 403 unless the form sent the session's token.
    public static function verify(): void
    {
        $sentToken = $_POST['csrf_token'] ?? '';
        if (!is_string($sentToken) || !hash_equals(self::token(), $sentToken)) {
            http_response_code(403);
            echo 'Formularen er udløbet. Gå tilbage, genindlæs siden og prøv igen';
            exit;
        }
    }
}
