<?php

declare(strict_types=1);

namespace PostCard\Services;

use PostCard\Core\Config;
use PostCard\Repositories\UserTokenRepository;

// Makes a one-time verification link and e-mails it. Used by register, "Send linket igen" and settings.
class EmailVerificationService
{
    public function sendLink(int $userId, string $email, string $firstName, string $invite = ''): bool
    {
        $token = bin2hex(random_bytes(32));

        $tokens = new UserTokenRepository();
        $tokens->create($userId, hash('sha256', $token), 'verify_email', 24 * 60);

        $link = Config::get('APP_URL') . '/verify-email?token=' . $token;
        if ($invite !== '') {
            $link .= '&invite=' . urlencode($invite);
        }

        $subject = 'Bekræft din e-mail til PostCard';
        $text = 'Hej ' . $firstName . ",\n\n"
            . "Tak fordi du har oprettet en bruger på PostCard.\n"
            . "Klik på linket for at bekræfte din e-mail. Linket virker i 24 timer.\n\n"
            . $link . "\n\n"
            . "Har du ikke oprettet en bruger, kan du se bort fra denne mail.\n";

        try {
            $mailer = new Mailer();
            $mailer->send($email, $firstName, $subject, $text);
        } catch (\RuntimeException $e) {
            error_log('Verification mail not sent: ' . $e->getMessage());
            return false;
        }

        return true;
    }
}
