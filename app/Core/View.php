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

        // The header shows who is logged in on every page, so it is fetched here once, not in every controller
        $currentUser = Auth::user();

        $viewsFolder = __DIR__ . '/../../views';

        require $viewsFolder . '/partials/header.php';
        require $viewsFolder . '/pages/' . $page . '.php';
        require $viewsFolder . '/partials/footer.php';
    }
}
