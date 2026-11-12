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

        // Henter gruppens ID fra URL'en gennem den fælles helper
        $groupId = queryId('id');

        // Stopper, hvis gruppe-ID'et mangler eller er ugyldigt
        if ($groupId === null) {
            abort(400, 'Ugyldigt gruppe-ID');
        }

        // Kontrollerer, at brugeren er medlem af gruppen
        Auth::requireMember($groupId);

        // Henter gruppens oplysninger fra databasen
        $groupRepository = new GroupRepository();
        $group = $groupRepository->findById($groupId);

        // Kontrollerer, at gruppen findes
        if (!$group) {
            abort(404, 'Gruppen blev ikke fundet');
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


    // Viser en gruppe for operatoren, også selvom operatoren ikke er medlem
    public function showAsOperator(): void
    {
        // Kontrollerer, at brugeren er operator
        Auth::requireOperator();

        // Henter gruppens ID fra URL'en
        $groupId = queryId('id');

        if ($groupId === null) {
            abort(400, 'Ugyldigt gruppe-ID');
        }

        // Henter gruppens oplysninger
        $groupRepository = new GroupRepository();
        $group = $groupRepository->findById($groupId);

        if (!$group) {
            abort(404, 'Gruppen blev ikke fundet');
        }

        // Henter gruppens medlemmer og oplevelser
        $memberRepository = new GroupMemberRepository();
        $memberCount = $memberRepository->countActiveMembers($groupId);
        $members = $memberRepository->findFirstMembersByGroup($groupId);

        $experienceRepository = new ExperienceRepository();
        $experiences = $experienceRepository->findVisibleByGroup($groupId);

        // Genbruger gruppesiden i operator-visning
        View::render('group', [
            'pageTitle' => 'Administration · ' . $group['name'],
            'pageStyle' => 'group',
            'pageScripts' => ['confirm'],
            'isLoggedIn' => true,
            'group' => $group,
            'memberCount' => $memberCount,
            'memberRole' => null,
            'members' => $members,
            'experiences' => $experiences,
            'operatorView' => true
        ]);
    }

}