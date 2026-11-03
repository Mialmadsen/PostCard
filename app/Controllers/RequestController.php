<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\Auth;
use PostCard\Core\View;
use PostCard\Repositories\ExperienceRepository;
use PostCard\Repositories\GroupMemberRepository;
use PostCard\Core\Session;

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

        // Henter den valgte gruppe fra URL'en med den fælles helper
        $groupId = queryId('group_id');

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

        if ($groupId !== null) {

            // Kontrollerer, at brugeren er administrator for gruppen
            Auth::requireGroupAdmin($groupId);

            // Henter oplevelser, der afventer godkendelse i gruppen
            $requests = $this->experienceRepository->findPendingByGroup($groupId);

        } elseif (isset($_GET['group_id'])) {

            // Stopper, hvis gruppe-ID'et er ugyldigt
            abort(400, 'Ugyldigt gruppe-ID.');
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
            'requests' => $requests,
            'success' => Session::takeFlash('success')
        ]);
    }


    // Behandler en administrators godkendelse eller afvisning af en oplevelse
    public function update(): void
    {
        // Brugeren skal være logget ind
        Auth::requireLogin();

        // Henter ID'et på oplevelsen fra formularen med den fælles helper
        $experienceId = inputId('experience_id');

        // Henter administratorens beslutning
        $decision = input('decision');

        // Kontrollerer, at oplevelsens ID og beslutningen er gyldige
        if ($experienceId === null || !in_array($decision, ['approve', 'reject'], true)) {
            abort(400, 'Ugyldig anmodning.');
        }

        // Finder oplevelsen i databasen
        $experience = $this->experienceRepository->find($experienceId);

        // Stopper, hvis oplevelsen ikke findes eller ikke afventer godkendelse
        if ($experience === null || $experience['experience_status'] !== 'pending') {
            abort(404, 'Anmodningen findes ikke længere.');
        }

        // Finder gruppen, som oplevelsen tilhører
        $groupId = (int) $experience['group_id'];

        // Kun en aktiv gruppeadministrator må behandle anmodningen
        Auth::requireGroupAdmin($groupId);

        // Hvis administratoren godkender oplevelsen
        if ($decision === 'approve') {

            // Henter datoer og opslagsgrænse fra formularen
            $startDate = input('start_date');
            $endDate = input('end_date');

            // Henter opslagsgrænsen fra formularen og kontrollerer, at det er et heltal
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
                abort(400, 'Kontroller datoerne og antal opslag.');
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
            abort(409, 'Anmodningen er allerede blevet behandlet.');
        }

        // Gemmer en bekræftelsesbesked som en flash message
        // Beskeden afhænger af, om oplevelsen blev godkendt eller afvist
        Session::flash(
            'success',
            $decision === 'approve'
                ? 'Oplevelsen er blevet godkendt.'
                : 'Oplevelsen er blevet afvist.'
);

        // Sender administratoren tilbage til gruppens anmodninger gennem helper-funktionen redirect()
        redirect('/requests?group_id=' . $groupId);
    }

}
