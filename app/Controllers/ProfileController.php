<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\Auth;
use PostCard\Core\View;
use PostCard\Repositories\GroupRepository;
use PostCard\Repositories\UserRepository;
use PostCard\Repositories\ExperienceRepository;
use PostCard\Services\ExperienceService;

// User can manage the profile
class ProfileController extends BaseController
{
    public function show(): void
    {
        // only logged-in people have a profile
        $userId = $this->requireUser();


        // the same row the header uses: first name, last name, username
        $user = Auth::user();

        $groups = new GroupRepository();
        $userGroups = $groups->findByUser($userId);
        $users = new UserRepository();
        $stats = $users->findAlbumStats($userId);

        $experiences = new ExperienceRepository();
        $experienceService = new ExperienceService();

        // My experiences sorted into one list per group, each with its Danish status text
        $experiencesByGroup = [];
        foreach ($experiences->findByUser($userId) as $experience) {
            $status = $experienceService->getStatus($experience);
            $experience['statusText'] = $status['statusText'];
            $experiencesByGroup[$experience['group_id']][] = $experience;
        }


        View::render('profile', [
            'pageTitle' => 'Min profil',
            'isLoggedIn' => true,
            'pageStyle' => 'profile',
            'user' => $user,
            'groups' => $userGroups,
            'stats' => $stats,
            'experiencesByGroup' => $experiencesByGroup,
        ]);
    }
}