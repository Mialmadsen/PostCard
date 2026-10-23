<?php
declare(strict_types=1);

// Controlleren håndterer requests til oplevelser.
// Den henter data gennem repositories og bruger ExperienceService til at beregne opslaggrænser og oplevelsens aktuelle status.
// Til sidst sender den oplysningerne videre til vores views.

namespace PostCard\Controllers;

// Repositories til at hente og gemme data i databasen
use PostCard\Repositories\ExperienceRepository;
use PostCard\Repositories\PostRepository;
use PostCard\Repositories\GroupMemberRepository;

// Fælles klasser til visning og adgangskontrol
use PostCard\Core\View;
use PostCard\Core\Auth;

// Service med regler og beregninger for oplevelser
use PostCard\Services\ExperienceService;

class ExperienceController
{
    // Repositories og service-objekter gemmes som private properties, så de kan bruges i controllerens metoder
    private ExperienceRepository $experienceRepository;
    private PostRepository $postRepository;
    private GroupMemberRepository $groupMemberRepository;
    private ExperienceService $experienceService;

    // Constructoren kører automatisk, når controlleren oprettes
    public function __construct()
    {
        // Opretter vores repository-objekter
        $this->experienceRepository = new ExperienceRepository();
        $this->postRepository = new PostRepository();
        $this->groupMemberRepository = new GroupMemberRepository();

        // Opretter vores service-objekt
        $this->experienceService = new ExperienceService();
    }

    // Viser en bestemt oplevelse gennem routeren
    public function show(): void
    {
        // Kontrollerer, om brugeren er logget ind
        Auth::requireLogin();

        // Henter experience-ID fra URL'en, fx ?id=1
        $experienceId = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        // Kontrollerer, at ID'et er gyldigt
        if (!$experienceId || $experienceId < 1) {
            http_response_code(400);
            exit('Ugyldigt experience-ID.');
        }

        // Henter oplevelsen og alle oplysninger til siden
        $data = $this->getExperienceData($experienceId);

        // Sender oplysningerne videre til vores experience-view
        View::render('experience', array_merge($data, [
            'pageTitle' => $data['experience']['name'],
            'pageStyle' => 'experience',
            'isLoggedIn' => true
        ]));
    }

    // Viser formularen til at oprette en ny oplevelse
    public function create(): void
    {
        // Kontrollerer, at brugeren er logget ind
        Auth::requireLogin();

        // Henter den indloggede brugers ID
        $userId = Auth::userId();

        // Henter alle grupper, som brugeren er aktivt medlem af
        $groups = $this->groupMemberRepository->findActiveGroupsByUser($userId);

        // Henter eventuelt et gruppe-ID fra URL'en
        $selectedGroupId = filter_input(INPUT_GET, 'group_id', FILTER_VALIDATE_INT);

        // Kontrollerer, at den valgte gruppe findes blandt brugerens grupper
        $allowedGroupIds = array_map('intval', array_column($groups, 'group_id'));

        if ($selectedGroupId && !in_array($selectedGroupId, $allowedGroupIds, true)) {
            http_response_code(403);
            exit('Du har ikke adgang til denne gruppe.');
        }

        // Sender grupperne og det valgte gruppe-ID videre til formularen
        View::render('create-experience', [
            'pageTitle' => 'Opret oplevelse',
            'pageStyle' => 'create-experience',
            'isLoggedIn' => true,
            'groups' => $groups,
            'selectedGroupId' => $selectedGroupId
        ]);
    }

    // Modtager formularen og opretter en ny oplevelse
    public function store(): void
    {
        // Kontrollerer, at brugeren er logget ind
        Auth::requireLogin();

        // Henter den indloggede brugers ID
        $userId = Auth::userId();

        // Henter oplysningerne fra formularen
        $groupId = filter_input(
            INPUT_POST,
            'group_id',
            FILTER_VALIDATE_INT
        );

        // Henter formularens oplysninger gennem den fælles input helper
        $name = input('name');
        $description = input('description');
        $startDate = input('start_date');
        $endDate = input('end_date');

        // Kontrollerer, at der er valgt en gyldig gruppe
        if (!$groupId || $groupId < 1) {
            http_response_code(400);
            exit('Du skal vælge en gyldig gruppe.');
        }

        // Kontrollerer, at brugeren er aktivt medlem af gruppen
        Auth::requireMember($groupId);

        // Kontrollerer oplevelsens navn og beskrivelse
        if (
            $name === '' ||
            mb_strlen($name) > 100 ||
            mb_strlen($description) > 1000
        ) {
            http_response_code(400);
            exit('Kontroller oplevelsens navn og beskrivelse.');
        }

        // Opretter dato-objekter til validering
        $start = \DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $startDate
        );

        $end = \DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $endDate
        );

        // Kontrollerer, at datoerne er gyldige kalenderdatoer
        // og at slutdatoen ikke ligger før startdatoen
        if (
            $start === false ||
            $end === false ||
            $start->format('Y-m-d') !== $startDate ||
            $end->format('Y-m-d') !== $endDate ||
            $end < $start
        ) {
            http_response_code(400);
            exit('Vælg en gyldig periode. Slutdatoen må ikke være før startdatoen.');
        }

        // Gemmer oplevelsen gennem ExperienceRepository
        // Repository bestemmer, om status skal være approved eller pending
        $experienceId = $this->experienceRepository->create(
            $userId,
            $groupId,
            $name,
            $description,
            $startDate,
            $endDate
        );

        // Sender brugeren videre til den nye oplevelse gennem helper-funktionen redirect()
        redirect('/experience?id=' . $experienceId);
    }

    // Henter og samler oplysninger til experience-siden
    private function getExperienceData(int $experienceId): array
    {
        // Henter oplevelsen fra databasen
        $experience = $this->experienceRepository->find($experienceId);

        // Viser en 404-fejl, hvis oplevelsen ikke findes
        if ($experience === null) {
            http_response_code(404);
            exit('Oplevelsen blev ikke fundet.');
        }

        // Kontrollerer, at brugeren er aktivt medlem af gruppen
        Auth::requireMember((int) $experience['group_id']);

        // Henter alle opslag, der hører til oplevelsen
        $posts = $this->postRepository->findByExperience($experienceId);

        // Danske månedsforkortelser til datoerne på opslagene
        $months = [
            1 => 'jan',
            2 => 'feb',
            3 => 'mar',
            4 => 'apr',
            5 => 'maj',
            6 => 'jun',
            7 => 'jul',
            8 => 'aug',
            9 => 'sep',
            10 => 'okt',
            11 => 'nov',
            12 => 'dec'
        ];

        // Gennemgår hvert opslag og gør oplysningerne klar til viewet
        foreach ($posts as &$post) {

            // Omdanner opslagets dato til et tidsstempel
            $timestamp = strtotime($post['date']);

            // Formaterer datoen til visning
            $post['day'] = date('d', $timestamp);
            $post['month'] = $months[(int) date('n', $timestamp)];
            $post['relative_date'] = date('d.m.Y', $timestamp);

            // Henter opslagets billedsti
            $image = $post['image'] ?? '';

            // Kontrollerer, om billedet er en ekstern URL
            if (
                is_string($image) &&
                filter_var($image, FILTER_VALIDATE_URL)
            ) {
                // Eksterne billeder, fx Cloudinary, bruges direkte
                $post['image'] = $image;

            } elseif (is_string($image) && $image !== '') {

                // Lokale billeder får den korrekte URL gennem asset()
                $post['image'] = asset($image);

            } else {

                // Hvis opslaget mangler et billede, bruges standardbilledet
                $post['image'] = asset(
                    'assets/images/sample/photo-1.jpg'
                );
            }
        }

        // Afslutter referencen fra foreach-løkken
        unset($post);

        // Tæller antallet af opslag i oplevelsen
        $postsUsed = count($posts);

        // ExperienceService beregner opslaggrænse og resterende opslag
        $postCounts = $this->experienceService->getPostCounts(
            $experience,
            $postsUsed
        );

        // ExperienceService beregner status og resterende dage
        $experienceStatus = $this->experienceService->getStatus(
            $experience
        );

        // Samler alle oplysningerne i ét array
        // Nøglerne er de samme som før, så viewet ikke skal ændres
        return array_merge(
            [
                'experience' => $experience,
                'posts' => $posts
            ],
            $postCounts,
            $experienceStatus
        );
    }
}
