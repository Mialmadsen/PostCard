<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\View;

// Simple public page: landing, rules, contact
class PageController
{
    public function landing(): void
    {
        View::render('landing', [
            'pageTitle' => 'Velkommen',
            'isLoggedIn' => false,
            'pageStyle' => 'landing',
        ]);
    }

    public function rules(): void
    {
        View::render('rules', [
            'pageTitle' => 'Regler',
            'isLoggedIn' => false,
        ]);
    }

    public function contact(): void
    {
        View::render('contact', [
            'pageTitle' => 'Kontakt',
            'isLoggedIn' => false,
        ]);
    }
}
