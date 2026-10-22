<?php
declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\Auth;
use PostCard\Core\View;
use PostCard\Repositories\GroupRepository;
use PostCard\Repositories\GroupMemberRepository;
use PostCard\Repositories\ExperienceRepository;

class GroupPageController
{
    // Viser gruppesiden for en bestemt gruppe
    public function show(): void
    {
        // Kontrollerer, at brugeren er logget ind
        Auth::requireLogin();

        // Henter gruppens ID fra URL'en
        $groupId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        // Kontrollerer, at gruppe-ID'et er gyldigt
        if (!$groupId || $groupId < 1) {
            http_response_code(400);
            echo 'Ugyldigt gruppe-ID';
            return;
        }

        // Kontrollerer, at brugeren er medlem af gruppen
        Auth::requireMember($groupId);

        // Henter gruppens oplysninger fra databasen
        $groupRepository = new GroupRepository();
        $group = $groupRepository->findById($groupId);

        // Kontrollerer, at gruppen findes
        if (!$group) {
            http_response_code(404);
            echo 'Gruppen blev ikke fundet';
            return;
        }

        // Henter antallet af aktive medlemmer, brugerens rolle og de første medlemmer i gruppen
        $memberRepository = new GroupMemberRepository();
        $memberCount = $memberRepository->countActiveMembers($groupId);
        $memberRole = $memberRepository->findActiveRole(Auth::userId(), $groupId);
        $members = $memberRepository->findFirstMembersByGroup($groupId);
        $experienceRepository = new ExperienceRepository();
        $experiences = $experienceRepository->findVisibleByGroup($groupId);

        // Sender gruppens oplysninger videre til viewet
        View::render('group', [
            'pageTitle' => $group['name'],
            'pageStyle' => 'group',
            'pageScripts' => ['confirm'],
            'isLoggedIn' => true,
            'group' => $group,
            'memberCount' => $memberCount,
            'memberRole' => $memberRole,
            'members' => $members,
            'experiences' => $experiences,
        ]);
    }
}