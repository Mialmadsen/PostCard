<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\Auth;
use PostCard\Core\View;
use PostCard\Repositories\AdminSettingsRepository;

class AdminSettingsController
{
    private AdminSettingsRepository $adminSettingsRepository;


    // Opretter repositoryet, som henter og opdaterer indstillinger.
    public function __construct()
    {
        $this->adminSettingsRepository = new AdminSettingsRepository();
    }


    // Viser operatorens indstillinger.
    public function index(): void
    {
        // Kun operatorer må se siden.
        Auth::requireOperator();

        $this->renderPage();
    }


    // Gemmer ændringer fra formularen.
    public function update(): void
    {
        // Kun operatorer må ændre indstillinger.
        Auth::requireOperator();

        // CSRF kontrolleres centralt i public/index.php.

        // Henter værdierne fra formularen.
        $postLimit = $_POST['default_post_limit'] ?? null;
        $uploadSize = $_POST['max_upload_size'] ?? null;
        $allowLikes = $_POST['allow_likes'] ?? null;
        $allowComments = $_POST['allow_comments'] ?? null;

        // Kontrollerer at talfelterne indeholder tekst.
        if (!is_string($postLimit) || !is_string($uploadSize)) {
            abort(400, 'Ugyldige formularoplysninger.');
        }

        // Kontrollerer at checkboxene kun indeholder 1 eller er tomme.
        if (
            ($allowLikes !== null && $allowLikes !== '1') ||
            ($allowComments !== null && $allowComments !== '1')
        ) {
            abort(400, 'Ugyldige formularoplysninger.');
        }

        // Fjerner unødvendige mellemrum.
        $postLimit = trim($postLimit);
        $uploadSize = trim($uploadSize);

        // Kontrollerer at tallene er hele tal i det tilladte interval.
        if (
            !ctype_digit($postLimit) ||
            !ctype_digit($uploadSize) ||
            (int) $postLimit < 1 ||
            (int) $postLimit > 50 ||
            (int) $uploadSize < 1 ||
            (int) $uploadSize > 20
        ) {
            $this->renderPage(
                'Antal opslag skal være mellem 1 og 50, og billedstørrelsen mellem 1 og 20 MB.'
            );
            return;
        }

        // Gemmer checkboxene som 1 eller 0.
        $likesValue = $allowLikes === '1' ? '1' : '0';
        $commentsValue = $allowComments === '1' ? '1' : '0';

        // Billedstørrelsen gemmes med MB, ligesom i databasen.
        $settings = [
            'default_post_limit' => (string) (int) $postLimit,
            'max_upload_size' => (string) (int) $uploadSize . 'MB',
            'allow_likes' => $likesValue,
            'allow_comments' => $commentsValue,
        ];

        // Opdaterer de fire eksisterende indstillinger.
        foreach ($settings as $settingKey => $value) {
            $updated = $this->adminSettingsRepository->update(
                $settingKey,
                $value
            );

            if (!$updated) {
                abort(404, 'Indstillingen blev ikke fundet.');
            }
        }

        // Sender operatoren tilbage efter gemning.
        header('Location: ' . url('/admin/settings?saved=1'), true, 303);
        exit;
    }


    // Viser siden og eventuelle fejlbeskeder.
    private function renderPage(?string $error = null): void
    {
        // Henter de aktuelle indstillinger fra databasen.
        $settings = $this->adminSettingsRepository->findAll();

        // Kontrollerer om indstillingerne lige er gemt.
        $saved = ($_GET['saved'] ?? null) === '1';

        // Sender data til vores view.
        View::render('admin-settings', [
            'pageTitle' => 'Administration · Indstillinger',
            'pageStyle' => 'admin',
            'isLoggedIn' => true,
            'isOperator' => true,
            'adminPage' => 'settings',
            'settings' => $settings,
            'saved' => $saved,
            'error' => $error,
        ]);
    }
}
