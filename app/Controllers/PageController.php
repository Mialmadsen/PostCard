<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\View;
use PostCard\Core\Auth;

// Simple public page: landing, rules, contact
class PageController
{
    public function landing(): void
    {
        View::render('landing', [
            'pageTitle' => 'Velkommen',
            'isLoggedIn' => Auth::userId() !== null,
            'pageStyle' => 'landing',
        ]);
    }

    public function rules(): void
    {
        View::render('rules', [
            'pageTitle' => 'Regler',
            'isLoggedIn' => Auth::userId() !== null,
        ]);
    }

    public function contact(): void
    {
        View::render('contact', [
            'pageTitle' => 'Kontakt',
            'isLoggedIn' => Auth::userId() !== null,
        ]);
    }
}
