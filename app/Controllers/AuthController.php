<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\View;
use PostCard\Repositories\UserRepository;

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

    // Handle the sent login form
    public function login(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $users = new UserRepository();
        $user = $users->findByEmail($email);

        $isPasswordCorrect = $user !== null && password_verify($password, $user['password_hash']);

        if (!$isPasswordCorrect) {
            View::render('login', [
                'pageTitle' => 'Log ind',
                'isLoggedIn' => false,
                'error' => 'Forkert e-mail eller adgangskode.',
                'email' => $email,
            ]);
            return;
        }

        // A blocked or deleted account may not log in
        if ($user['user_status'] !== 'active') {
            View::render('login', [
                'pageTitle' => 'Log ind',
                'isLoggedIn' => false,
                'error' => 'Din bruger er spærret. Kontakt os, hvis du mener, det er en fejl.',
                'email' => $email,
            ]);
            return;
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['user_id'];
        // temporary, should be '/home', when moved to views
        header('Location: ' . BASE_PATH . '/home.php');
        exit;
    }

    // Log out: empty the session, delete its cookie, destroy it, go to the front page
    public function logout(): void
    {
        // empty the locker
        $_SESSION = [];

        // delete cookie
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);

        // Destroy the session
        session_destroy();

        header('Location: ' . BASE_PATH . '/');
        exit;
    }
}
