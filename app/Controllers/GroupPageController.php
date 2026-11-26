<?php
declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\Auth;
use PostCard\Core\View;
use PostCard\Repositories\GroupRepository;
use PostCard\Repositories\GroupMemberRepository;
use PostCard\Repositories\ExperienceRepository;


class GroupPageController extends BaseController
{
    // Viser gruppesiden for en bestemt gruppe
    public function show(): void
    {
        // Kontrollerer, at brugeren er logget ind
        $userId = $this->requireUser();

        // Henter og validerer gruppens ID fra URL'en
        $groupId = $this->requireQueryId('id', 'Ugyldigt gruppe-ID');

        // Kontrollerer, at brugeren er medlem af gruppen
        Auth::requireMember($groupId);

        // Henter gruppens oplysninger, medlemmer og oplevelser
        $data = $this->getGroupData($groupId, $userId);

        // Sender gruppens oplysninger videre til viewet
        View::render('group', [
            'pageTitle' => $data['group']['name'],
            'pageStyle' => 'group',
            'pageScripts' => ['confirm'],
            'isLoggedIn' => true,
            'group' => $data['group'],
            'memberCount' => $data['memberCount'],
            'memberRole' => $data['memberRole'],
            'members' => $data['members'],
            'experiences' => $data['experiences'],
        ]);
    }


    // Viser en gruppe for operatoren, også selvom operatoren ikke er medlem
    public function showAsOperator(): void
    {
        // Kontrollerer, at brugeren er operator
        Auth::requireOperator();

        // Henter og validerer gruppens ID fra URL'en
        $groupId = $this->requireQueryId('id', 'Ugyldigt gruppe-ID');

        // Henter gruppens oplysninger, medlemmer og oplevelser
        $data = $this->getGroupData($groupId);

        // Genbruger gruppesiden i operator-visning
        View::render('group', [
            'pageTitle' => 'Administration · ' . $data['group']['name'],
            'pageStyle' => 'group',
            'pageScripts' => ['confirm'],
            'isLoggedIn' => true,
            'group' => $data['group'],
            'memberCount' => $data['memberCount'],
            'memberRole' => null,
            'members' => $data['members'],
            'experiences' => $data['experiences'],
            'operatorView' => true
        ]);
    }


    // Henter de fælles oplysninger til gruppesiden og operator-visningen
    private function getGroupData(int $groupId, ?int $userId = null): array
    {
        // Henter gruppens oplysninger fra databasen
        $groupRepository = new GroupRepository();
        $group = $groupRepository->findById($groupId);

        // Kontrollerer, at gruppen findes
        if (!$group) {
            abort(404, 'Gruppen blev ikke fundet');
        }

        // Henter antallet af aktive medlemmer
        $memberRepository = new GroupMemberRepository();
        $memberCount = $memberRepository->countActiveMembers($groupId);

        // Henter brugerens rolle, hvis der er angivet et bruger-ID
        $memberRole = null;

        if ($userId !== null) {
            $memberRole = $memberRepository->findActiveRole($userId, $groupId);
        }

        // Henter de første medlemmer i gruppen
        $members = $memberRepository->findFirstMembersByGroup($groupId);

        // Henter gruppens synlige oplevelser
        $experienceRepository = new ExperienceRepository();
        $experiences = $experienceRepository->findVisibleByGroup($groupId);

        // Returnerer de oplysninger, som gruppesiden skal bruge
        return [
            'group' => $group,
            'memberCount' => $memberCount,
            'memberRole' => $memberRole,
            'members' => $members,
            'experiences' => $experiences,
        ];
    }

}