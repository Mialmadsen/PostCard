<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\Auth;
use PostCard\Core\View;
use PostCard\Repositories\UserRepository;
use PostCard\Repositories\GroupRepository;

// User can manage the profile
class ProfileController
{
    public function show(): void
    {
        // only logged-in people have a profile
        Auth::requireLogin();


        $users = new UserRepository();
        $user = $users->findById(Auth::userId());

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
