<?php

declare(strict_types=1);

// Full address for a route: '/login' → '/PostCard/public/login'
function url(string $path): string
{
    $address = BASE_PATH . $path;
    return e($address);
}
