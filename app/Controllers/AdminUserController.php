<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\Auth;
use PostCard\Core\View;
use PostCard\Repositories\UserRepository;

// The operator's pages about users: the list, and blocking or unblocking a user.
class AdminUserController
{
    // Shows the list of all users.
    public function index(): void
    {
        // Only operators may be here. Everyone else gets 403.
        Auth::requireOperator();

        $users = new UserRepository();
        $allUsers = $users->findAllForAdmin();

        View::render('admin-users', [
            'pageTitle' => 'Administration · Brugere',
            'pageStyle' => 'admin',
            'pageScripts' => ['confirm'],
            'isLoggedIn' => true,
            'isOperator' => true,
            'adminPage' => 'users',
            'users' => $allUsers,
        ]);
    }
}
