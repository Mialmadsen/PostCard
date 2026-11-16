<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\View;
use PostCard\Core\Auth;
use PostCard\Repositories\AdminContentRepository;

// Simple public page: landing, rules, contact
class PageController
{
    // Viser forsiden.
    public function landing(): void
    {
        // Henter de redigerbare tekster fra databasen.
        $contentRepository = new AdminContentRepository();
        $siteContent = $contentRepository->findAll();

        View::render('landing', [
            'pageTitle' => 'Velkommen',
            'isLoggedIn' => Auth::userId() !== null,
            'pageStyle' => 'landing',
            'siteContent' => $siteContent,
        ]);
    }

    // Viser regler og information om brugerdata.
    public function rules(): void
    {
        // Henter de redigerbare tekster fra databasen.
        $contentRepository = new AdminContentRepository();
        $siteContent = $contentRepository->findAll();

        View::render('rules', [
            'pageTitle' => 'Regler',
            'isLoggedIn' => Auth::userId() !== null,
            'siteContent' => $siteContent,
        ]);
    }

    // Viser kontaktsiden.
    public function contact(): void
    {
        // Henter de redigerbare tekster fra databasen.
        $contentRepository = new AdminContentRepository();
        $siteContent = $contentRepository->findAll();

        View::render('contact', [
            'pageTitle' => 'Kontakt',
            'isLoggedIn' => Auth::userId() !== null,
            'siteContent' => $siteContent,
        ]);
    }
}
