<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\Auth;
use PostCard\Core\View;
use PostCard\Repositories\AdminContentRepository;

class AdminContentController
{
    private AdminContentRepository $adminContentRepository;

    // Opretter repositoryet, som henter og opdaterer indhold.
    public function __construct()
    {
        $this->adminContentRepository = new AdminContentRepository();
    }

    // Viser siden med alle indholdsområder.
    public function index(): void
    {
        // Kun operatorer må se siden.
        Auth::requireOperator();
        $this->renderPage();
    }

    // Gemmer ændringer fra en formular.
    public function update(): void
    {
        // Kun operatorer må opdatere indhold.
        Auth::requireOperator();

        // CSRF kontrolleres centralt i public/index.php.

        // Henter oplysningerne fra formularen.
        $contentKey = $_POST['content_key'] ?? null;
        $title = $_POST['title'] ?? null;
        $body = $_POST['body'] ?? null;

        // Kontrollerer at alle værdier er tekst.
        if (
            !is_string($contentKey) ||
            !is_string($title) ||
            !is_string($body)
        ) {
            abort(400, 'Ugyldige formularoplysninger.');
        }

        // Kontrollerer at indholdsområdet er tilladt.
        if (!in_array(
            $contentKey,
            AdminContentRepository::ALLOWED_KEYS,
            true
        )) {
            abort(400, 'Ugyldigt indholdsområde.');
        }

        // Fjerner unødvendige mellemrum.
        $title = trim($title);
        $body = trim($body);

        // Kontrollerer at felterne ikke er tomme.
        if ($title === '' || $body === '') {
            $this->renderPage(
                'Du skal udfylde både overskrift og tekst.',
                $contentKey,
                $title,
                $body
            );
            return;
        }

        // Kontrollerer længden på serveren.
        // Det beskytter også, hvis HTML-validering omgås.
        if (
            mb_strlen($title, 'UTF-8') > 150 ||
            mb_strlen($body, 'UTF-8') > 10000
        ) {
            $this->renderPage(
                'Overskriften må højst være 150 tegn, og teksten højst 10.000 tegn.',
                $contentKey,
                $title,
                $body
            );
            return;
        }

        // Finder den operator, der er logget ind.
        $operatorId = Auth::userId();

        if ($operatorId === null) {
            abort(403, 'Du skal være logget ind.');
        }

        // Opdaterer indholdet gennem repositoryet.
        $updated = $this->adminContentRepository->update(
            $contentKey,
            $title,
            $body,
            $operatorId
        );

        // Hvis området ikke findes i databasen.
        if (!$updated) {
            abort(404, 'Indholdsområdet blev ikke fundet.');
        }

        // Sender operatoren tilbage efter gemning.
        // Redirect forhindrer gentagen POST ved opdatering.
        header(
            'Location: ' . url('/admin/content?saved=' . rawurlencode($contentKey)),
            true,
            303
        );
        exit;
    }

    // Viser siden og eventuelle fejlbeskeder.
    private function renderPage(
        ?string $error = null,
        ?string $editKey = null,
        string $editTitle = '',
        string $editBody = ''
    ): void {

        // Henter indholdet fra databasen.
        $content = $this->adminContentRepository->findAll();

        // Beholder indtastede værdier ved en fejl.
        if ($editKey !== null && isset($content[$editKey])) {
            $content[$editKey]['title'] = $editTitle;
            $content[$editKey]['body'] = $editBody;
        }

        // Kontrollerer om der er gemt en tekst.
        $saved = $_GET['saved'] ?? null;

        if (
            !is_string($saved) ||
            !in_array(
                $saved,
                AdminContentRepository::ALLOWED_KEYS,
                true
            )
        ) {
            $saved = null;
        }

        // Sender data til vores view.
        View::render('admin-content', [
            'pageTitle' => 'Administration · Indhold',
            'pageStyle' => 'admin',
            'isLoggedIn' => true,
            'isOperator' => true,
            'adminPage' => 'content',
            'content' => $content,
            'saved' => $saved,
            'error' => $error,
        ]);
    }
}
