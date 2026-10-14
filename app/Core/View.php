<?php

declare(strict_types=1);

namespace PostCard\Core;

// Puts a page together: header + page + footer.
class View
{
    public static function render(string $page, array $data = []): void
    {
        // Unpack the data: ['pageTitle' => 'Log ind'] → $pageTitle = 'Log ind'
        extract($data);

        $viewsFolder = __DIR__ . '/../../views';

        require $viewsFolder . '/partials/header.php';
        require $viewsFolder . '/pages/' . $page . '.php';
        require $viewsFolder . '/partials/footer.php';
    }
}
