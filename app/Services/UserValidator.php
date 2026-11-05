<?php

declare(strict_types=1);

namespace PostCard\Services;

use PostCard\Repositories\UserRepository;

// (your comment: what this class is for)
class UserValidator
{
    // Handles the sent data in register or settings form
    public function validateDetails(
        string $firstName,
        string $lastName,
        string $username,
        string $email,
        string $birthdate,
        int $exceptUserId = 0
    ): array {

        // Needed to check whether the username and email are free.
        $users = new UserRepository();

        // We collect all mistakes in one array, using the field name as the key.
        $errors = [];

        if ($firstName === '') {
            $errors['first_name'] = 'Skriv dit fornavn.';
        } elseif (mb_strlen($firstName) > 100) {
            $errors['first_name'] = 'Fornavnet må højst være 100 tegn.';
        }

        if ($lastName === '') {
            $errors['last_name'] = 'Skriv dit efternavn.';
        } elseif (mb_strlen($lastName) > 100) {
            $errors['last_name'] = 'Efternavnet må højst være 100 tegn.';
        }

        if ($username === '') {
            $errors['username'] = 'Vælg et brugernavn.';
        } elseif (mb_strlen($username) > 50) {
            $errors['username'] = 'Brugernavnet må højst være 50 tegn.';
        } elseif (!preg_match('/^[\p{L}\p{N}_.]+$/u', $username)) {
            $errors['username'] = 'Brugernavnet må kun indeholde bogstaver, tal, _ og .';
        } elseif ($users->usernameExists($username, $exceptUserId)) {
            $errors['username'] = 'Brugernavnet er optaget. Vælg et andet.';
        }

        if ($email === '') {
            $errors['email'] = 'Skriv din e-mail.';
        } elseif (mb_strlen($email) > 255) {
            $errors['email'] = 'E-mailen må højst være 255 tegn.';
        } elseif (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $errors['email'] = 'Skriv en gyldig e-mail, fx navn@eksempel.dk.';
        } elseif ($users->emailExists($email, $exceptUserId)) {
            $errors['email'] = 'Der findes allerede en bruger med denne e-mail.';
        }

        $parsedBirthdate = \DateTimeImmutable::createFromFormat('!Y-m-d', $birthdate);
        $latestAllowed = new \DateTimeImmutable('today -13 years');

        if ($birthdate === '') {
            $errors['birthdate'] = 'Skriv din fødselsdato.';
        } elseif ($parsedBirthdate === false || $parsedBirthdate->format('Y-m-d') !== $birthdate) {
            $errors['birthdate'] = 'Skriv en gyldig dato.';
        } elseif ($parsedBirthdate > $latestAllowed) {
            $errors['birthdate'] = 'Du skal være mindst 13 år for at oprette en bruger.';
        }


        return $errors;
    }

    // we validate password
    public function validatePassword(string $password, string $passwordConfirm): array
    {
        $errors = [];


        // Minimum in characters (what the user sees), maximum in bytes (bcrypt ignores everything after 72 bytes).
        if (mb_strlen($password) < 12) {
            $errors['password'] = 'Adgangskoden skal være mindst 12 tegn.';
        } elseif (strlen($password) > 72) {
            $errors['password'] = 'Adgangskoden må højst være 72 tegn.';
        }

        if ($passwordConfirm !== $password) {
            $errors['password_confirm'] = 'De to adgangskoder er ikke ens.';
        }



        return $errors;
    }
}
