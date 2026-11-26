<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\View;
use PostCard\Repositories\GroupMemberRepository;
use PostCard\Repositories\ExperienceRepository;
use PostCard\Repositories\PostFeedRepository;
use PostCard\Repositories\CommentRepository;
use PostCard\Repositories\LikeRepository;
use PostCard\Repositories\UserRepository;

class HomeController extends BaseController
{
    // Viser forsiden for den indloggede bruger
    public function index(): void
    {
        // Kontrollerer login og henter den indloggede brugers ID
        $userId = $this->requireUser();

        // Henter den indloggede brugers fornavn
        $userRepository = new UserRepository();
        $firstName = $userRepository->findFirstNameById($userId);

        // Henter kun de grupper, hvor brugeren er aktivt medlem
        $groupMemberRepository = new GroupMemberRepository();
        $groups = $groupMemberRepository->findActiveGroupsByUser($userId);

        // Henter oplevelser, som brugeren selv har oprettet
        $experienceRepository = new ExperienceRepository();
        $experiences = $experienceRepository->findByUser($userId);

        // Henter populære og fastgjorte opslag fra brugerens grupper
        $postFeedRepository = new PostFeedRepository();
        $popularPosts = $postFeedRepository->findPopularByUser($userId);
        $stickyPosts = $postFeedRepository->findStickyByUser($userId);

        // Klargør like-status og billedadresser for opslagene
        $likeRepository = new LikeRepository();

        $popularPosts = $this->preparePosts(
            $popularPosts,
            $userId,
            $likeRepository
        );

        $stickyPosts = $this->preparePosts(
            $stickyPosts,
            $userId,
            $likeRepository
        );

        // Henter de nyeste kommentarer fra brugerens grupper
        $commentRepository = new CommentRepository();
        $latestComments = $commentRepository->findLatestByUser($userId);

        // Sender data videre til Home-viewet
        View::render('home', [
            'pageTitle' => 'Min forside',
            'pageStyle' => 'home',
            'pageScripts' => ['likes', 'confirm'],
            'isLoggedIn' => true,
            'firstName' => $firstName,
            'groups' => $groups,
            'experiences' => $experiences,
            'popularPosts' => $popularPosts,
            'stickyPosts' => $stickyPosts,
            'latestComments' => $latestComments,
        ]);
    }

    // Klargør like-status og billedadresser for en samling opslag
    private function preparePosts(
        array $posts,
        int $userId,
        LikeRepository $likeRepository
    ): array {
        foreach ($posts as &$post) {
            // Henter like-status, så knappen viser korrekt status
            $post['hasLiked'] = $likeRepository->hasLiked(
                (int) $post['id'],
                $userId
            );

            // Bruger den beskyttede billedrute til Cloudinary-billeder
            if (!empty($post['image_id'])) {
                $post['image'] = url(
                    '/post-image?id=' . (int) $post['image_id']
                );
            } else {
                // Opslag uden billeder får ingen billedadresse
                $post['image'] = null;
            }
        }

        // Afslutter referencen fra foreach-løkken
        unset($post);

        return $posts;
    }
}