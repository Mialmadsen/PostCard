<?php
declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Repositories\ExperienceRepository;
use PostCard\Repositories\PostFeedRepository;
use PostCard\Repositories\GroupMemberRepository;
use PostCard\Repositories\LikeRepository;
use PostCard\Core\View;
use PostCard\Core\Auth;
use PostCard\Services\ExperienceService;
use PostCard\Services\RateLimiter;
use PostCard\Repositories\AdminSettingsRepository;

class ExperienceController
{
    private ExperienceRepository $experienceRepository;
    private PostFeedRepository $postFeedRepository;
    private GroupMemberRepository $groupMemberRepository;
    private LikeRepository $likeRepository;
    private ExperienceService $experienceService;

    // Opretter repositories og service, som controlleren bruger.
    public function __construct()
    {
        $this->experienceRepository = new ExperienceRepository();
        $this->postFeedRepository = new PostFeedRepository();
        $this->groupMemberRepository = new GroupMemberRepository();
        $this->likeRepository = new LikeRepository();
        $this->experienceService = new ExperienceService();
    }

    // Viser en bestemt oplevelse.
    public function show(): void
    {
        Auth::requireLogin();
        $experienceId = queryId('id');
        if ($experienceId === null) {
            abort(400, 'Ugyldigt experience-ID.');
        }
        $data = $this->getExperienceData($experienceId);
        View::render('experience', array_merge($data, [
            'pageTitle' => $data['experience']['name'],
            'pageScripts' => ['likes', 'confirm'],
            'pageStyle' => 'experience',
            'isLoggedIn' => true,
        ]));
    }

    // Viser en experience for operatoren på tværs af alle grupper.
    public function showAsOperator(): void
    {
        Auth::requireOperator();

        $experienceId = queryId('id');
        if ($experienceId === null) {
            abort(400, 'Ugyldigt experience-ID.');
        }

        $data = $this->getExperienceData($experienceId, true);

        View::render('experience', array_merge($data, [
            'pageTitle' => 'Administration · ' . $data['experience']['name'],
            'pageScripts' => ['likes'],
            'pageStyle' => 'experience',
            'isLoggedIn' => true,
            'isOperator' => true,
            'operatorView' => true
        ]));
    }

    // Viser formularen til at oprette en ny oplevelse.
    public function create(): void
    {
        Auth::requireLogin();
        $userId = Auth::userId();
        $groups = $this->groupMemberRepository->findActiveGroupsByUser($userId);
        $selectedGroupId = queryId('group_id');
        $allowedGroupIds = array_map('intval', array_column($groups, 'group_id'));
        if ($selectedGroupId && !in_array($selectedGroupId, $allowedGroupIds, true)) {
            http_response_code(403);
            exit('Du har ikke adgang til denne gruppe.');
        }
        View::render('create-experience', [
            'pageTitle' => 'Opret oplevelse',
            'pageStyle' => 'create-experience',
            'isLoggedIn' => true,
            'groups' => $groups,
            'selectedGroupId' => $selectedGroupId
        ]);
    }

    // Modtager formularen og opretter en ny oplevelse.
    public function store(): void
    {
        Auth::requireLogin();
        $userId = Auth::userId();
        $groupId = inputId('group_id');
        $name = input('name');
        $description = input('description');
        $startDate = input('start_date');
        $endDate = input('end_date');

        if ($groupId === null) {
            abort(400, 'Du skal vælge en gyldig gruppe.');
        }

        Auth::requireMember($groupId);

        if ($name === '' || mb_strlen($name) > 100 || mb_strlen($description) > 1000) {
            http_response_code(400);
            exit('Kontroller oplevelsens navn og beskrivelse.');
        }

        $start = \DateTimeImmutable::createFromFormat('!Y-m-d', $startDate);
        $end = \DateTimeImmutable::createFromFormat('!Y-m-d', $endDate);

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

        // Spam-værn: højst 10 oplevelser på 24 timer og højst 1 pr. minut.
        // Begge grænser er defineret i RateLimiter.
        $limiter = new RateLimiter();

        foreach (['create_experience', 'create_experience_burst'] as $action) {
            if ($limiter->tooMany($action, $userId)) {
                http_response_code(429);

                // Henter grupperne igen, så formularen kan vises korrekt.
                $groups = $this->groupMemberRepository->findActiveGroupsByUser($userId);

                // Viser formularen med fejlbeskeden og de indtastede værdier.
                View::render('create-experience', [
                    'pageTitle' => 'Opret oplevelse',
                    'pageStyle' => 'create-experience',
                    'isLoggedIn' => true,
                    'groups' => $groups,
                    'selectedGroupId' => $groupId,
                    'createError' => $limiter->message($action),
                    'oldName' => $name,
                    'oldDescription' => $description,
                    'oldStartDate' => $startDate,
                    'oldEndDate' => $endDate
                ]);

                return;
            }
        }

        // Forsøger at oprette oplevelsen.
        // Repository kontrollerer, om brugeren opretter for hurtigt.
        try {
            $experienceId = $this->experienceRepository->create(
                $userId,
                $groupId,
                $name,
                $description,
                $startDate,
                $endDate
            );

        } catch (\RuntimeException $e) {

            // Andre fejl skal stadig håndteres af den normale fejlhåndtering.
            if ($e->getCode() !== 429) {
                throw $e;
            }

            // Brugeren har forsøgt at oprette oplevelser for hurtigt.
            http_response_code(429);

            // Henter grupperne igen, så formularen kan vises korrekt.
            $groups = $this->groupMemberRepository->findActiveGroupsByUser($userId);

            // Viser formularen med fejlbeskeden og de indtastede værdier.
            View::render('create-experience', [
                'pageTitle' => 'Opret oplevelse',
                'pageStyle' => 'create-experience',
                'isLoggedIn' => true,
                'groups' => $groups,
                'selectedGroupId' => $groupId,
                'createError' => $e->getMessage(),
                'oldName' => $name,
                'oldDescription' => $description,
                'oldStartDate' => $startDate,
                'oldEndDate' => $endDate
            ]);

            return;
        }

        // Oprettelsen lykkedes, så brugeren sendes til oplevelsen.
        redirect('/experience?id=' . $experienceId);
    }

    // Sletter en oplevelse, hvis den tilhører den indloggede bruger.
    public function delete(): void
    {
        Auth::requireLogin();

        // Sletning må kun udføres gennem en POST-request.
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            abort(405, 'Sletning kræver en POST-request.');
        }

        $experienceId = inputId('experience_id');
        if ($experienceId === null) {
            abort(400, 'Ugyldigt experience-ID.');
        }

        // Henter oplevelsen, så vi kan kontrollere ejerskab og finde gruppen.
        $experience = $this->experienceRepository->find($experienceId);
        if ($experience === null) {
            abort(404, 'Oplevelsen blev ikke fundet.');
        }

        if ((int) $experience['user_id'] !== Auth::userId()) {
            abort(403, 'Du kan kun slette dine egne oplevelser.');
        }

        $groupId = (int) $experience['group_id'];

        // Servicen sletter oplevelsen fra databasen og rydder op i Cloudinary.
        $this->experienceService->deleteExperience($experienceId, Auth::userId());

        // Sender brugeren tilbage til gruppen efter sletningen.
        redirect('/group?id=' . $groupId);
    }

    // Henter og samler oplysninger til experience-siden.
    private function getExperienceData(int $experienceId, bool $operatorView = false): array
    {
        $experience = $this->experienceRepository->find($experienceId);
        if ($experience === null) {
            abort(404, 'Oplevelsen blev ikke fundet.');
        }

        // Almindelige brugere skal være medlemmer af gruppen.
        // Operatorens adgang er allerede kontrolleret i showAsOperator().
        if (!$operatorView) {
            Auth::requireMember((int) $experience['group_id']);
        }

        $posts = $this->postFeedRepository->findByExperience($experienceId);
        $userId = Auth::userId();

        // Henter indstillingerne én gang til alle opslag på oplevelsen.
        $siteSettings = (new AdminSettingsRepository())->findAll();
        $likesEnabled = ($siteSettings['allow_likes'] ?? '0') === '1';

        foreach ($posts as &$post) {
            // Finder ud af, om den indloggede bruger allerede har liket opslaget.
            $post['hasLiked'] = $this->likeRepository->hasLiked((int) $post['id'], $userId);
            $post['day'] = danishDate($post['date'], 'd');
            $post['month'] = danishDate($post['date'], 'MMM');
            $post['relative_date'] = danishDate($post['date'], 'dd.MM.y');

            // Billeder hentes gennem den beskyttede billedrute.
            if (!empty($post['image_id'])) {
                $post['image'] = url('/post-image?id=' . (int) $post['image_id']);
            } else {
                $post['image'] = null;
            }
        }

        unset($post);

        $postsUsed = count($posts);
        $postCounts = $this->experienceService->getPostCounts($experience, $postsUsed);
        $experienceStatus = $this->experienceService->getStatus($experience);

        return array_merge([
            'experience' => $experience,
            'posts' => $posts,
            'likesEnabled' => $likesEnabled
        ], $postCounts, $experienceStatus);
    }
}
