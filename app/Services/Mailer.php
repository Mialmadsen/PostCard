<?php

declare(strict_types=1);

namespace PostCard\Services;

require_once __DIR__ . '/../Libraries/PHPMailer/Exception.php';
require_once __DIR__ . '/../Libraries/PHPMailer/SMTP.php';
require_once __DIR__ . '/../Libraries/PHPMailer/PHPMailer.php';

use PostCard\Core\Config;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as MailException;


class Mailer
{
    public function send(string $toEmail, string $toName, string $subject, string $text): void
    {
        $mode = Config::get('MAIL_MODE');

        if ($mode === 'log') {
            $this->writeToLog($toEmail, $toName, $subject, $text);
            return;
        }

        if ($mode === 'smtp') {
            $this->sendSmtp($toEmail, $toName, $subject, $text);
            return;
        }

        throw new \RuntimeException('Unknown MAIL_MODE in .env: ' . $mode);
    }

    private function writeToLog(string $toEmail, string $toName, string $subject, string $text): void
    {
        $folder = __DIR__ . '/../../storage/logs';

        if (!is_dir($folder)) {
            mkdir($folder, 0775, true);
        }

        $entry = date('Y-m-d H:i:s') . "\n"
            . 'To: ' . $toName . ' <' . $toEmail . ">\n"
            . 'Subject: ' . $subject . "\n\n"
            . $text . "\n"
            . str_repeat('-', 60) . "\n";

        file_put_contents($folder . '/mail.log', $entry, FILE_APPEND | LOCK_EX);
    }

    private function sendSmtp(string $toEmail, string $toName, string $subject, string $text): void
    {
        $mail = new PHPMailer(true);

        $mail->isSMTP();

        $mail->Host = Config::get('MAIL_HOST');
        $mail->Port = (int) Config::get('MAIL_PORT');

        $mail->SMTPAuth = true;
        $mail->Username = Config::get('MAIL_USERNAME');
        $mail->Password = Config::get('MAIL_PASSWORD');

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;

        $mail->CharSet = PHPMailer::CHARSET_UTF8;

        $mail->setFrom(Config::get('MAIL_FROM'), Config::get('MAIL_FROM_NAME'));
        $mail->addAddress($toEmail, $toName);

        $mail->Subject = $subject;
        $mail->Body = $text;

        try {
            $mail->send();
        } catch (MailException $e) {
            throw new \RuntimeException('Mail not sent: ' . $mail->ErrorInfo, 0, $e);
        }
    }
}
