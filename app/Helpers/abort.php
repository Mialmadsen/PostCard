<?php

declare(strict_types=1);

use PostCard\Core\Auth;
use PostCard\Core\Config;
use PostCard\Core\View;

function abort(int $code, string $message): never
{
    // 1. Tell the browser the status code (404, 403 …)
    http_response_code($code);

    // 2. Show the error page with the postman, and what went wrong
    View::render('error', [
        'pageTitle' => 'Fejl ' . $code,
        'isLoggedIn' => Auth::userId() !== null,
        'pageStyle' => 'error',
        'code' => $code,
        'message' => $message,
    ]);

    // 3. Stop the script
    exit;
}

// A crash nobody caught, e.g. the database is down or a method gets the wrong type.
// index.php hands every such crash to this function with set_exception_handler().
function handleUncaughtException(\Throwable $e): void
{
    // 1. The full technical reason goes to the PHP error log, never to the visitor
    error_log('Uncaught ' . get_class($e) . ': ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());

    // 2. On my own PC the reason is also shown on the page, so I can fix it; on the live site only a friendly text
    $isLocal = Config::get('APP_ENV') === 'local';
    $message = $isLocal
        ? get_class($e) . ': ' . $e->getMessage() . ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')'
        : 'Noget gik galt hos os.';

    try {
        abort(500, $message);
    } catch (\Throwable) {
        // 3. The error page failed too (its header reads the database): plain text is all that is left
        http_response_code(500);
        echo 'Noget gik galt. Prøv igen om lidt.';
    }
}
