<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\Auth;
use PostCard\Core\View;
use PostCard\Repositories\UserRepository;
use PostCard\Services\UserValidator;

// Login, register, logout and password reset.
class AuthController
{
    public function showLogin(): void
    {
        $invite = filter_input(INPUT_GET, 'invite');
        $invite = is_string($invite) ? $invite : '';

        View::render('login', [
            'pageTitle' => 'Log ind',
            'isLoggedIn' => false,
            'invite' => $invite,
        ]);
    }

    // Handle the sent login form
    public function login(): void
    {
        $email = mb_strtolower(input('email'));
        $password = input('password', false);
        $invite = input('invite');
        $users = new UserRepository();
        $user = $users->findByEmail($email);

        $isPasswordCorrect = $user !== null && password_verify($password, $user['password_hash']);

        if (!$isPasswordCorrect) {
            View::render('login', [
                'pageTitle' => 'Log ind',
                'isLoggedIn' => false,
                'error' => 'Forkert e-mail eller adgangskode.',
                'email' => $email,
                'invite' => $invite,
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
                'invite' => $invite,
            ]);
            return;
        }

        Auth::login($user['user_id']);

        if ($invite !== '') {
            redirect('/join?token=' . urlencode($invite));
        }

        redirect('/home');
    }

    // Log out: destroy the session, then go to the front page
    public function logout(): void
    {
        Auth::logout();
        redirect('/');
    }

    // Show the empty registration form.
    public function showRegister(): void
    {
        $invite = filter_input(INPUT_GET, 'invite');
        $invite = is_string($invite) ? $invite : '';

        View::render('register', [
            'pageTitle' => 'Opret bruger',
            'isLoggedIn' => false,
            'errors' => [],
            'invite' => $invite,
            'old' => []
        ]);
    }

    // Handle the sent register form
    public function register(): void
    {
        $firstName = input('first_name');
        $lastName = input('last_name');
        $username = input('username');
        $email = mb_strtolower(input('email'));
        $birthdate = input('birthdate');
        $password = input('password', false);
        $passwordConfirm = input('password_confirm', false);
        $acceptsRules = input('accept_rules') === '1';
        $invite = input('invite');
        $users = new UserRepository();
        $validator = new UserValidator();

        $errors = $validator->validateDetails($firstName, $lastName, $username, $email, $birthdate);


        // Minimum in characters (what the user sees), maximum in bytes (bcrypt ignores everything after 72 bytes).
        if (mb_strlen($password) < 12) {
            $errors['password'] = 'Adgangskoden skal være mindst 12 tegn.';
        } elseif (strlen($password) > 72) {
            $errors['password'] = 'Adgangskoden må højst være 72 tegn.';
        }

        if ($passwordConfirm !== $password) {
            $errors['password_confirm'] = 'De to adgangskoder er ikke ens.';
        }

        if (!$acceptsRules) {
            $errors['accept_rules'] = 'Du skal acceptere reglerne.';
        }

        // We check if there are any errors and either stop and send the person back to form, or continue and create a new user
        if ($errors !== []) {
            http_response_code(422);
            View::render('register', [
                'pageTitle' => 'Opret bruger',
                'isLoggedIn' => false,
                'errors' => $errors,
                'invite' => $invite,
                'old' => [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => $username,
                    'email' => $email,
                    'birthdate' => $birthdate,
                ],
            ]);
            return;
        }

        // The happy end of registration
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $newUserId = $users->create(
            $firstName,
            $lastName,
            $username,
            $email,
            $birthdate,
            $passwordHash
        );

        Auth::login($newUserId);

        if ($invite !== '') {
            redirect('/join?token=' . urlencode($invite));
        }

        redirect('/home');
    }
}
