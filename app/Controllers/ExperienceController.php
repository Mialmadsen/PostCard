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
            'pageStyle' => 'experience',
            'isLoggedIn' => true
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
        $experienceId = $this->experienceRepository->create(
            $userId,
            $groupId,
            $name,
            $description,
            $startDate,
            $endDate
        );
        redirect('/experience?id=' . $experienceId);
    }

    // Henter og samler oplysninger til experience-siden.
    private function getExperienceData(int $experienceId): array
    {
        $experience = $this->experienceRepository->find($experienceId);
        if ($experience === null) {
            abort(404, 'Oplevelsen blev ikke fundet.');
        }
        Auth::requireMember((int) $experience['group_id']);
        $posts = $this->postFeedRepository->findByExperience($experienceId);
        $userId = Auth::userId();
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
            'posts' => $posts
        ], $postCounts, $experienceStatus);
    }
}
