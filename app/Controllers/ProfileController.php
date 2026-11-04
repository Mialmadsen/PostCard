<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\Auth;
use PostCard\Core\View;
use PostCard\Repositories\GroupRepository;

// User can manage the profile
class ProfileController
{
    public function show(): void
    {
        // only logged-in people have a profile
        Auth::requireLogin();


        // the same row the header uses: first name, last name, username
        $user = Auth::user();

        $groups = new GroupRepository();
        $userGroups = $groups->findByUser(Auth::userId());


        View::render('profile', [
            'pageTitle' => 'Min profil',
            'isLoggedIn' => true,
            'pageStyle' => 'profile',
            'user' => $user,
            'groups' => $userGroups
        ]);
    }
}
