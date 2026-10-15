<?php
//filen bruger ExperienceRepository og PostRepository til at hente oplevelsen og dens opslag fra databasen. 
// Controlleren behandler derefter oplysningerne, så de er klar til at blive vist. 
// Den formaterer blandt andet datoer, vælger midlertidige billeder, beregner opslagstælleren og finder oplevelsens aktuelle status. 
// Til sidst returnerer den alle oplysningerne i et array, som vi bruger i vores view.

// TODO: Kontroller at den indloggede bruger er aktivt medlem
// af gruppen, før oplevelsen og dens opslag vises.

declare(strict_types=1);

// Fortæller, hvor klassen hører til i projektet
namespace PostCard\Controllers;

// Gør det muligt at bruge vores to repositories
use PostCard\Repositories\ExperienceRepository;
use PostCard\Repositories\PostRepository;
use PostCard\Core\View;
use PostCard\Core\Auth;

// Controlleren samler og behandler data til experience-siden
class ExperienceController
{
    // Variabler til vores to repositories
    // private betyder, at de kun kan bruges inde i klassen
    private ExperienceRepository $experienceRepository;
    private PostRepository $postRepository;

    // Constructoren kører automatisk, når vi opretter controlleren
    public function __construct()
    {
        // Opretter objekter af vores to repository-klasser
        $this->experienceRepository = new ExperienceRepository();
        $this->postRepository = new PostRepository();
    }


    // Viser en bestemt experience gennem routeren
    public function show(): void
    {
        // Kontrollerer, om brugeren er logget ind
        // Hvis ikke, bliver brugeren sendt til login-siden
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

        // TODO: Kontroller at den indloggede bruger er aktivt medlem af gruppen, før oplevelsen og dens opslag bliver vist.
        // Henter experience og posts gennem vores eksisterende kode
        $data = $this->getExperienceData($experienceId);

        // Sender data videre til View-klassen
        View::render('experience', array_merge($data, [
            'pageTitle' => $data['experience']['name'],
            'pageStyle' => 'experience',
            'isLoggedIn' => true
        ]));
    }

    // Henter alle oplysninger, der skal bruges på experience-siden
    // Modtager oplevelsens ID og returnerer et array med data

    private function getExperienceData(int $experienceId): array
    {
        // Henter oplevelsen fra databasen gennem ExperienceRepository
        $experience = $this->experienceRepository->find($experienceId);

        // Hvis oplevelsen ikke findes, viser vi en 404-fejl
        if ($experience === null) {
            http_response_code(404);
            exit('Oplevelsen blev ikke fundet.');
        }

        // Henter alle opslag, der hører til oplevelsen
        $posts = $this->postRepository->findByExperience($experienceId);

        // Array med danske forkortelser for månederne
        // Bruges til datoerne på opslagene
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

        // Gennemgår hvert opslag og gør oplysningerne klar til HTML
        // & betyder, at vi ændrer direkte i opslagets array
        foreach ($posts as &$post) {

            // Omdanner datoen fra databasen til et tidsstempel
            $timestamp = strtotime($post['date']);

            // Gemmer dag, måned og dato i et format, vi kan vise
            $post['day'] = date('d', $timestamp);
            $post['month'] = $months[(int) date('n', $timestamp)];
            $post['relative_date'] = date('d.m.Y', $timestamp);

            // Henter billedstien fra opslaget, hvis der findes en
            $image = $post['image'] ?? '';

            // Kontrollerer, om billedet er en ekstern URL eller en lokal billedsti
            if (is_string($image) && filter_var($image, FILTER_VALIDATE_URL)) {

                // Eksterne billeder, fx Cloudinary, bruges direkte
                $post['image'] = $image;
            } elseif (is_string($image) && $image !== '') {

                // Lokale billeder får den korrekte URL gennem asset()
                $post['image'] = asset($image);
            } else {

                // Hvis opslaget ikke har et billede, bruges et standardbillede
                $post['image'] = asset('assets/images/sample/photo-1.jpg');
            }
        }

        // Afslutter referencen fra foreach-løkken
        unset($post);

        // Tæller hvor mange opslag oplevelsen har
        $postsUsed = count($posts);

        // Henter oplevelsens opslaggrænse fra databasen
        // Hvis der ikke er angivet en grænse, bruger vi null
        $postLimit = $experience['post_limit'] !== null
            ? (int) $experience['post_limit']
            : null;

        // Beregner hvor mange opslag der er tilbage
        // max(0, ...) sikrer, at resultatet ikke bliver negativt
        $postsRemaining = $postLimit !== null
            ? max(0, $postLimit - $postsUsed)
            : null;

        // Henter dags dato og oplevelsens status fra databasen
        $today = date('Y-m-d');
        $status = $experience['experience_status'];

        // Oversætter oplevelsens status til dansk
        // Vi tager også højde for oplevelsens start- og slutdato
        if ($status === 'pending') {

            // Oplevelsen venter på godkendelse
            $statusText = 'Afventer godkendelse';
        } elseif ($status === 'rejected') {

            // Oplevelsen er blevet afvist
            $statusText = 'Afvist';
        } elseif (
            $status === 'closed' ||
            $experience['end_date'] < $today
        ) {

            // Oplevelsen er lukket eller slutdatoen er passeret
            $statusText = 'Afsluttet';
        } elseif ($experience['start_date'] > $today) {

            // Oplevelsen er godkendt, men er endnu ikke begyndt
            $statusText = 'Kommende';
        } else {

            // Oplevelsen er i gang
            $statusText = 'I gang';
        }

        // Kontrollerer om oplevelsen er godkendt og aktiv i dag
        // Dette kontrollerer IKKE, om brugeren har adgang til den
        $isActive = (
            $status === 'approved' &&
            $experience['start_date'] <= $today &&
            $experience['end_date'] >= $today
        );

        // Variabel til antal dage tilbage
        // null betyder, at vi endnu ikke har beregnet et antal
        $daysRemaining = null;

        // Beregner kun dage tilbage, hvis oplevelsen er aktiv
        if ($isActive) {

            // Opretter dato-objekter for i dag og slutdatoen
            $todayDate = new \DateTimeImmutable($today);
            $endDate = new \DateTimeImmutable($experience['end_date']);

            // Finder forskellen mellem de to datoer i dage
            $daysRemaining = (int) $todayDate->diff($endDate)->days;
        }

        // Returnerer alle oplysningerne i ét samlet array
        // public/experience.php kan derefter sende dem videre til viewet
        return [
            'experience' => $experience,
            'posts' => $posts,
            'postsUsed' => $postsUsed,
            'postLimit' => $postLimit,
            'postsRemaining' => $postsRemaining,
            'statusText' => $statusText,
            'isActive' => $isActive,
            'daysRemaining' => $daysRemaining
        ];
    }
}
