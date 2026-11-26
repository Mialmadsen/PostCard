<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\Auth;
use PostCard\Core\Session;

// BaseController is a parentcontroller and provides common functionality for all controllers.
abstract class BaseController
{
    // Requires login and returns the authenticated user's ID.
    protected function requireUser(): int
    {
        Auth::requireLogin();

        return Auth::userId();
    }

    // Requires a valid ID from a GET request.
    protected function requireQueryId(
        string $key,
        string $message,
        int $statusCode = 400
    ): int {
        $id = queryId($key);

        if ($id === null) {
            abort($statusCode, $message);
        }

        return $id;
    }

    // Requires a valid ID from a POST request.
    protected function requireInputId(
        string $key,
        string $message,
        int $statusCode = 400
    ): int {
        $id = inputId($key);

        if ($id === null) {
            abort($statusCode, $message);
        }

        return $id;
    }

    // Stores a flash message and redirects the user.
    protected function redirectWithFlash(
        string $key,
        string $message,
        string $path
    ): never {
        Session::flash($key, $message);

        redirect($path);
    }
}