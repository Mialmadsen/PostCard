<?php

declare(strict_types=1);

// When PHP meets an unknown class, load its file from app/
spl_autoload_register(function (string $className): void {
    $prefix = 'PostCard\\';
    if (!str_starts_with($className, $prefix)) {
        return;
    }

    // 'PostCard\Core\Router' → 'Core\Router'
    $relativeClass = substr($className, strlen($prefix));

    // 'Core\Router' → '.../app/Core/Router.php'
    $filePath = __DIR__ . '/' . str_replace('\\', '/', $relativeClass) . '.php';

    if (is_file($filePath)) {
        require $filePath;
    }
});
