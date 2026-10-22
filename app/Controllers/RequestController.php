<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\Auth;
use PostCard\Core\View;
use PostCard\Repositories\ExperienceRepository;
use PostCard\Repositories\GroupMemberRepository;

class RequestController
{
    private ExperienceRepository $experienceRepository;
    private GroupMemberRepository $groupMemberRepository;

    // Opretter de repositories, vi skal bruge til Requests
    public function __construct()
    {
        $this->experienceRepository = new ExperienceRepository();
        $this->groupMemberRepository = new GroupMemberRepository();
    }

    // Henter administratorens grupper og de afventende oplevelser
    public function index(): void
    {
        // Kontrollerer, at brugeren er logget ind
        Auth::requireLogin();

        // Henter den indloggede brugers ID
        $userId = Auth::userId();

        // Finder alle grupper, hvor brugeren er administrator
        $groups = $this->groupMemberRepository->findAdminGroupsByUser($userId);

        // Henter den valgte gruppe fra URL'en
        $groupId = filter_input(
            INPUT_GET,
            'group_id',
            FILTER_VALIDATE_INT
        );

        // Hvis ingen gruppe er valgt, vælges den første administratorgruppe
        if (!isset($_GET['group_id']) && !empty($groups)) {
            $groupId = (int) $groups[0]['group_id'];
        }

        // Som udgangspunkt har vi ingen afventende anmodninger
        $requests = [];

        // Gemmer navnet på den valgte gruppe
        $groupName = '';

        // Gennemgår brugerens administratorgrupper
        foreach ($groups as $group) {

            // Finder den gruppe, som brugeren har valgt
            if ((int) $group['group_id'] === (int) $groupId) {
                $groupName = $group['name'];
                break;
            }
}

        if ($groupId !== null && $groupId !== false && $groupId > 0) {

            // Kontrollerer, at brugeren er administrator for gruppen
            Auth::requireGroupAdmin($groupId);

            // Henter oplevelser, der afventer godkendelse i gruppen
            $requests = $this->experienceRepository->findPendingByGroup($groupId);

        } elseif (isset($_GET['group_id'])) {

            // Stopper, hvis gruppe-ID'et er ugyldigt
            http_response_code(400);
            exit('Ugyldigt gruppe-ID.');
        }

        // Sender data videre til Requests-viewet
        View::render('requests', [
            'pageTitle' => 'Anmodninger',
            'pageStyle' => 'requests',
            'pageScripts' => ['confirm'],
            'isLoggedIn' => true,
            'groups' => $groups,
            'groupId' => $groupId,
            'groupName' => $groupName,
            'requests' => $requests
        ]);
    }


    // Behandler en administrators godkendelse eller afvisning af en oplevelse
    public function update(): void
    {
        // Brugeren skal være logget ind
        Auth::requireLogin();

        // Henter ID'et på oplevelsen fra formularen
        $experienceId = filter_input(
            INPUT_POST,
            'experience_id',
            FILTER_VALIDATE_INT
        );

        // Henter administratorens beslutning
        $decision = $_POST['decision'] ?? '';

        // Kontrollerer, at oplevelsens ID og beslutningen er gyldige
        if (!$experienceId || !in_array($decision, ['approve', 'reject'], true)) {
            http_response_code(400);
            exit('Ugyldig anmodning.');
        }

        // Finder oplevelsen i databasen
        $experience = $this->experienceRepository->find($experienceId);

        // Stopper, hvis oplevelsen ikke findes eller ikke afventer godkendelse
        if ($experience === null || $experience['experience_status'] !== 'pending') {
            http_response_code(404);
            exit('Anmodningen findes ikke længere.');
        }

        // Finder gruppen, som oplevelsen tilhører
        $groupId = (int) $experience['group_id'];

        // Kun en aktiv gruppeadministrator må behandle anmodningen
        Auth::requireGroupAdmin($groupId);

        // Hvis administratoren godkender oplevelsen
        if ($decision === 'approve') {

            // Henter datoer og opslagsgrænse fra formularen
            $startDate = trim((string) ($_POST['start_date'] ?? ''));
            $endDate = trim((string) ($_POST['end_date'] ?? ''));

            $postLimit = filter_input(
                INPUT_POST,
                'post_limit',
                FILTER_VALIDATE_INT
            );

            // Kontrollerer, at datoerne er gyldige kalenderdatoer
            $start = \DateTimeImmutable::createFromFormat('!Y-m-d', $startDate);
            $end = \DateTimeImmutable::createFromFormat('!Y-m-d', $endDate);

            if (
                $start === false ||
                $end === false ||
                $start->format('Y-m-d') !== $startDate ||
                $end->format('Y-m-d') !== $endDate ||
                $end < $start ||
                $postLimit === false ||
                $postLimit === null ||
                $postLimit < 1 ||
                $postLimit > 50
            ) {
                http_response_code(400);
                exit('Kontroller datoerne og antal opslag.');
            }

            // Godkender oplevelsen og gemmer eventuelle ændringer
            $updated = $this->experienceRepository->updateRequest(
                $experienceId,
                $groupId,
                'approved',
                $startDate,
                $endDate,
                $postLimit
            );

        } else {

            // Afviser oplevelsen uden at ændre datoer eller opslagsgrænse
            $updated = $this->experienceRepository->updateRequest(
                $experienceId,
                $groupId,
                'rejected'
            );
        }

        // Hvis oplevelsen allerede er blevet behandlet, vises en fejl
        if (!$updated) {
            http_response_code(409);
            exit('Anmodningen er allerede blevet behandlet.');
        }

        // Gemmer en bekræftelsesbesked i sessionen
        // Beskeden afhænger af, om oplevelsen blev godkendt eller afvist
        $_SESSION['request_success'] = $decision === 'approve'
        ? 'Oplevelsen er blevet godkendt.'
        : 'Oplevelsen er blevet afvist.';

        // Sender administratoren tilbage til gruppens anmodninger
        header('Location: ' . url('/requests?group_id=' . $groupId));
        exit;
    }

}
