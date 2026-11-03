<?php

declare(strict_types=1);


function abort(int $code, string $message): never
{
    // 1. Tell the browser the status code (404, 403 …)
    http_response_code($code);

    // 2. Tell the visitor what went wrong
    echo e($message);

    // 3. Stop the script
    exit;
}
