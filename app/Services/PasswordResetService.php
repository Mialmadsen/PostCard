<?php

declare(strict_types=1);

namespace PostCard\Services;

use PostCard\Core\Config;
use PostCard\Repositories\UserRepository;
use PostCard\Repositories\UserTokenRepository;

class PasswordResetService
{
    private const TOKEN_TYPE = 'reset_password';
    private const TOKEN_MINUTES = 60;

    // Finder brugeren og sender et link til nulstilling af adgangskoden.
    public function sendLink(string $email): bool
    {
        $email = mb_strtolower(trim($email));

        // Kontrollerer, at e-mailadressen er gyldig.
        if (
            strlen($email) > 255 ||
            filter_var($email, FILTER_VALIDATE_EMAIL) === false
        ) {
            return false;
        }

        // Finder brugeren ud fra e-mailadressen.
        $users = new UserRepository();
        $user = $users->findByEmail($email);

        // Sender kun links til aktive brugere med bekræftet e-mail.
        if (
            $user === null ||
            $user['user_status'] !== 'active' ||
            $user['email_verified_at'] === null
        ) {
            return false;
        }

        $userId = (int) $user['user_id'];
        $firstName = $user['first_name'];

        // Anvender brugerens registrerede e-mailadresse.
        $email = $user['email'];

        $tokens = new UserTokenRepository();

        // Genererer et sikkert, tilfældigt engangstoken.
        $token = bin2hex(random_bytes(32));

        // Kontrollerer begrænsninger og opretter tokenet i samme transaktion.
        $tokenId = $tokens->createPasswordReset(
            $userId,
            $email,
            hash('sha256', $token),
            self::TOKEN_MINUTES
        );

        // Stopper anmodningen, hvis der ikke kunne oprettes et nyt token.
        if ($tokenId === null) {
            return false;
        }

        // Opretter linket til siden, hvor adgangskoden nulstilles.
        $link = rtrim(Config::get('APP_URL'), '/')
            . '/reset-password?token=' . $token;

        // Opretter e-mailens emne og indhold.
        $subject = 'Nulstil din adgangskode til PostCard';

        $text = 'Hej ' . $firstName . ",\n\n"
            . "Vi har modtaget en anmodning om at nulstille din adgangskode til PostCard.\n\n"
            . "Klik på linket nedenfor for at vælge en ny adgangskode. "
            . "Linket virker i 1 time.\n\n"
            . $link . "\n\n"
            . "Hvis du ikke har anmodet om en ny adgangskode, "
            . "kan du se bort fra denne mail.\n\n"
            . "Venlig hilsen\n"
            . "PostCard";

        try {
            $mailer = new Mailer();
            $mailer->send($email, $firstName, $subject, $text);

            return true;

        } catch (\RuntimeException $e) {
            // Ugyldiggør tokenet, hvis e-mailen ikke kunne sendes.
            $tokens->markUsed($tokenId);

            // Logger fejlen uden at afsløre følsomme oplysninger.
            error_log('Password reset mail not sent.');

            return false;
        }
    }

    // Kontrollerer, at tokenet har det forventede format.
    private function hasValidFormat(string $token): bool
    {
        return strlen($token) === 64 && ctype_xdigit($token);
    }

    // Kontrollerer, om brugerens reset-link stadig er gyldigt.
    public function isValidToken(string $token): bool
    {
        if (!$this->hasValidFormat($token)) {
            return false;
        }

        $tokens = new UserTokenRepository();

        return $tokens->findValid(
            hash('sha256', $token),
            self::TOKEN_TYPE
        ) !== null;
    }



    // Validerer og gemmer brugerens nye adgangskode.
    public function resetPassword(
        string $token,
        string $password,
        string $passwordConfirm
    ): array {
        if (!$this->hasValidFormat($token)) {
            return [
                'token' => 'Linket er ugyldigt eller udløbet.'
            ];
        }

        // Genbruger de eksisterende adgangskodekrav.
        $validator = new UserValidator();

        $errors = $validator->validatePassword(
            $password,
            $passwordConfirm
        );

        if ($errors !== []) {
            return $errors;
        }

        // Hasher den nye adgangskode.
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $tokens = new UserTokenRepository();

        // Ændrer adgangskoden og forbruger tokenet.
        $changed = $tokens->resetPassword(
            hash('sha256', $token),
            $passwordHash
        );

        if (!$changed) {
            return [
                'token' => 'Linket er ugyldigt eller udløbet.'
            ];
        }

        return [];
    }
}