<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\Auth;
use PostCard\Core\View;
use PostCard\Repositories\UserRepository;
use PostCard\Services\UserValidator;
use PostCard\Repositories\LoginAttemptRepository;
use PostCard\Services\EmailVerificationService;
use PostCard\Core\Session;
use PostCard\Repositories\UserTokenRepository;


// Login, register, logout and password reset.

class AuthController extends BaseController
{
    // After this many failed attempts in 15 minutes, login is paused for that e-mail
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const MAX_VERIFY_LINKS = 3;

    public function showLogin(): void
    {
        $invite = filter_input(INPUT_GET, 'invite');
        $invite = is_string($invite) ? $invite : '';
        $message = Session::takeFlash('login_message');

        View::render('login', [
            'pageTitle' => 'Log ind',
            'pageScripts' => ['password-toggle'],
            'isLoggedIn' => false,
            'invite' => $invite,
            'message' => $message
        ]);
    }

    // Handle the sent login form
    public function login(): void
    {
        $email = mb_strtolower(input('email'));
        $password = input('password', false);
        $invite = input('invite');

        // Longer than the database column: no real account can have it, so stop before any query
        if (mb_strlen($email) > 255) {
            View::render('login', [
                'pageTitle' => 'Log ind',
                'pageScripts' => ['password-toggle'],
                'isLoggedIn' => false,
                'error' => 'Forkert e-mail eller adgangskode.',
                'email' => '',
                'invite' => $invite,
            ]);
            return;
        }

        $attempts = new LoginAttemptRepository();

        if ($attempts->countRecent($email) >= self::MAX_LOGIN_ATTEMPTS) {
            http_response_code(429);
            View::render('login', [
                'pageTitle' => 'Log ind',
                'pageScripts' => ['password-toggle'],
                'isLoggedIn' => false,
                'error' => 'For mange forsøg. Prøv igen om 15 minutter.',
                'email' => $email,
                'invite' => $invite,
            ]);
            return;
        }

        $users = new UserRepository();
        $user = $users->findByEmail($email);

        $isPasswordCorrect = $user !== null && password_verify($password, $user['password_hash']);

        if (!$isPasswordCorrect) {
            $attempts->add($email);
            View::render('login', [
                'pageTitle' => 'Log ind',
                'pageScripts' => ['password-toggle'],
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
                'pageScripts' => ['password-toggle'],
                'isLoggedIn' => false,
                'error' => 'Din bruger er spærret. Kontakt os, hvis du mener, det er en fejl.',
                'email' => $email,
                'invite' => $invite,
            ]);
            return;
        }

        // Not confirmed yet: no login. Whoever knows the password gets a new link (at most 3 valid at a time)
        if ($user['email_verified_at'] === null) {
            $tokens = new UserTokenRepository();

            if ($tokens->countValid((int) $user['user_id'], 'verify_email') >= self::MAX_VERIFY_LINKS) {
                $error = 'Din e-mail er ikke bekræftet endnu. Vi har allerede sendt dig flere links. Tjek din indbakke og spam-mappe.';
            } else {
                $verification = new EmailVerificationService();
                $sent = $verification->sendLink((int) $user['user_id'], $email, $user['first_name'], $invite);

                $error = $sent
                    ? 'Din e-mail er ikke bekræftet endnu. Vi har sendt et nyt link til ' . $email . '.'
                    : 'Din e-mail er ikke bekræftet endnu, og vi kunne ikke sende et nyt link. Prøv igen senere.';
            }

            View::render('login', [
                'pageTitle' => 'Log ind',
                'pageScripts' => ['password-toggle'],
                'isLoggedIn' => false,
                'error' => $error,
                'email' => $email,
                'invite' => $invite,
            ]);
            return;
        }

        $attempts->clear($email);
        Auth::login($user['user_id']);

        if ($invite !== '') {
            redirect('/join?token=' . urlencode($invite));
        }

        // Operators start on their dashboard, everyone else on their own home
        if ($users->isOperator($user['user_id'])) {
            redirect('/admin');
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
            'pageScripts' => ['password-toggle'],
            'isLoggedIn' => false,
            'errors' => [],
            'invite' => $invite,
            'old' => []
        ]);
    }

    // Handle the sent register form
    public function register(): void
    {
        // A filled honeypot means a bot: stop quietly, before any database query, without saying why
        if (input('website') !== '') {
            redirect('/login');
        }

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


        $errors = $errors + $validator->validatePassword($password, $passwordConfirm);


        if (!$acceptsRules) {
            $errors['accept_rules'] = 'Du skal acceptere reglerne.';
        }

        // We check if there are any errors and either stop and send the person back to form, or continue and create a new user
        if ($errors !== []) {
            http_response_code(422);
            View::render('register', [
                'pageTitle' => 'Opret bruger',
                'pageScripts' => ['password-toggle'],
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


        $verification = new EmailVerificationService();
        $sent = $verification->sendLink($newUserId, $email, $firstName, $invite);


        $this->redirectWithFlash(
            'login_message',
            $sent
                ? 'Vi har sendt et link til ' . $email . '. Klik på det for at bekræfte din e-mail, og log så ind.'
                : 'Din bruger er oprettet, men vi kunne ikke sende mailen. Log ind for at få et nyt link.',
            '/login'
        );
    }

    // The link in the verification mail: /verify-email?token=…(&invite=…)
    public function verifyEmail(): void
    {
        $token = filter_input(INPUT_GET, 'token');
        $token = is_string($token) ? $token : '';
        $invite = filter_input(INPUT_GET, 'invite');
        $invite = is_string($invite) ? $invite : '';

        $tokens = new UserTokenRepository();
        $found = $tokens->findValid(hash('sha256', $token), 'verify_email');

        if ($found === null) {
            $this->redirectWithFlash(
                'login_message',
                'Linket er ugyldigt eller udløbet. Log ind for at få et nyt link.',
                '/login'
            );
        }

        if ($tokens->markUsed((int) $found['token_id']) === 1) {
            $users = new UserRepository();
            $users->markEmailVerified((int) $found['user_id']);
        }

        $this->redirectWithFlash(
            'login_message',
            'Din e-mail er bekræftet. Du kan nu logge ind.',
            $invite !== '' ? '/login?invite=' . urlencode($invite) : '/login'
        );
    }
}