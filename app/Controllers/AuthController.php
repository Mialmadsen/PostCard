<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\View;

// Login, register, logout and password reset.
class AuthController
{
    public function showLogin(): void
    {
        View::render('login', [
            'pageTitle' => 'Log ind',
            'isLoggedIn' => false,
        ]);
    }
}
