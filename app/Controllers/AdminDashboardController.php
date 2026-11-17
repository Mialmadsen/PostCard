<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\Auth;
use PostCard\Core\View;
use PostCard\Repositories\AdminDashboardRepository;
use PostCard\Repositories\UserRepository;

// The operator's front page: PostCard in numbers and the newest users.
class AdminDashboardController
{
    public function index(): void
    {
        // Only operators may be here. Everyone else gets 403.
        Auth::requireOperator();

        $dashboard = new AdminDashboardRepository();
        $users = new UserRepository();

        $counts = $dashboard->findPlatformCounts();
        $newestUsers = $users->findNewest();

        View::render('admin', [
            'pageTitle' => 'Administration · Oversigt',
            'pageStyle' => 'admin',
            'isLoggedIn' => true,
            'isOperator' => true,
            'adminPage' => 'overview',
            'counts' => $counts,
            'newestUsers' => $newestUsers,
        ]);
    }
}
