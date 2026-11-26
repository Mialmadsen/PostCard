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
    public function sendLink(string $email): void
    {
        $email = mb_strtolower(trim($email));

        // Kontrollerer, at e-mailadressen er gyldig.
        if (
            strlen($email) > 255 ||
            filter_var($email, FILTER_VALIDATE_EMAIL) === false
        ) {
            return;
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
            return;
        }

        $userId = (int) $user['user_id'];
        $firstName = $user['first_name'];

        $tokens = new UserTokenRepository();

        // Undgår at sende endnu et link, hvis brugeren allerede har et gyldigt.
        if ($tokens->countValid($userId, self::TOKEN_TYPE) > 0) {
            return;
        }

        // Genererer et sikkert, tilfældigt engangstoken.
        $token = bin2hex(random_bytes(32));

        // Gemmer kun tokenets hash i databasen.
        $tokenId = $tokens->create(
            $userId,
            hash('sha256', $token),
            self::TOKEN_TYPE,
            self::TOKEN_MINUTES
        );

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
        } catch (\RuntimeException $e) {
            // Ugyldiggør tokenet, hvis e-mailen ikke kunne sendes.
            $tokens->markUsed($tokenId);

            // Logger fejlen uden at vise den til brugeren.
            error_log('Password reset mail not sent: ' . $e->getMessage());
        }
    }
}