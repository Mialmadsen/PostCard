<?php

declare(strict_types=1);

// Full address for a route: '/login' → '/PostCard/public/login'
function url(string $path): string
{
    // TEMPORARY: an old page in public/ opened directly has no BASE_PATH (index.php never ran),
    // so work it out the same way index.php does. Remove when every page has moved.
    $basePath = defined('BASE_PATH') ? BASE_PATH : rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');

    $address = $basePath . $path;
    return e($address);
}
