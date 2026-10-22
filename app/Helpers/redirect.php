<?php

declare(strict_types=1);

//Every controller ends the successful form the same way , its data type is never, which means it never comes back it ends with exit
function redirect(string $path): never
{
    if (!str_starts_with($path, '/') || str_starts_with($path, '//')) {
        $path = '/';
    }

    header('Location: ' . BASE_PATH . $path, true, 303);
    exit;
}
