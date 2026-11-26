<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Repositories\LikeRepository;
use PostCard\Repositories\PostRepository;
use PostCard\Repositories\AdminSettingsRepository;


// Controlleren håndterer likes på opslag.
class LikeController extends BaseController
{
    private LikeRepository $likeRepository;
    private PostRepository $postRepository;

    // Opretter de repositories, controlleren skal bruge.
    public function __construct()
    {
        $this->likeRepository = new LikeRepository();
        $this->postRepository = new PostRepository();
    }

    // Tilføjer eller fjerner et like på et opslag.
    public function toggle(): void
    {
        // Kontrollerer, at brugeren er logget ind.
        $userId = $this->requireUser();

        // Kontrollerer, om operatoren har tilladt likes.
        $this->requireLikesEnabled();

        // Henter og validerer opslagets ID fra formularen.
        $postId = $this->requireInputId('post_id', 'Ugyldigt post-ID.');

        // Kontrollerer, at brugeren har adgang til opslaget.
        $this->requirePostAccess($postId, $userId);

        // Kontrollerer, om brugeren allerede har liket opslaget.
        $hasLiked = $this->likeRepository->hasLiked(
            $postId,
            $userId
        );

        if ($hasLiked) {
            // Fjerner liket, hvis brugeren allerede har liket opslaget.
            $this->likeRepository->delete($postId, $userId);
        } else {
            // Gemmer et like, hvis brugeren ikke har liket opslaget.
            $this->likeRepository->create($postId, $userId);
        }

        // Hvis anmodningen kommer fra JavaScript,
        // sender vi resultatet som JSON i stedet for at genindlæse siden.
        if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest') {
            header('Content-Type: application/json; charset=utf-8');

            echo json_encode([
                'success' => true,
                'liked' => !$hasLiked,
                'likes' => $this->likeRepository->countByPost($postId)
            ]);

            exit;
        }

        // Almindelig formular virker stadig uden JavaScript.
        redirect('/post?id=' . $postId);
    }


    // Henter en oversigt over brugere, der har liket et opslag.
    public function users(): void
    {
        // Brugeren skal være logget ind.
        $userId = $this->requireUser();

        // Kontrollerer, om operatoren har tilladt visning af likes.
        $this->requireLikesEnabled();

        // Henter og validerer opslagets ID fra URL'en.
        $postId = $this->requireQueryId('id', 'Ugyldigt post-ID.');

        // Kontrollerer, at brugeren har adgang til opslaget.
        $this->requirePostAccess($postId, $userId);

        // Henter brugerne, der har liket opslaget.
        $users = $this->likeRepository->findUsersByPost($postId);

        // Tilføjer en URL til brugerens profilbillede,
        // hvis brugeren har uploadet et.
        foreach ($users as &$user) {
            $user['avatar_url'] = null;

            if (!empty($user['profile_image'])) {
                $user['avatar_url'] = url(
                    '/profile-image?user_id=' . (int) $user['user_id']
                    . '&v=' . (int) $user['profile_image_version']
                );
            }
        }
        unset($user);


        // Sender resultatet som JSON til JavaScript.
        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'success' => true,
            'users' => $users
        ]);

        exit;
    }

    // Kontrollerer, om operatoren har aktiveret likes på PostCard.
    private function requireLikesEnabled(): void
    {
        $settings = (new AdminSettingsRepository())->findAll();

        if (($settings['allow_likes'] ?? '0') !== '1') {
            abort(403, 'Synes godt om er slået fra på PostCard.');
        }
    }

    // Kontrollerer, at opslaget findes, og at brugeren har adgang til det.
    private function requirePostAccess(int $postId, int $userId): void
    {
        // Repository kontrollerer adgang til gruppen, som opslaget tilhører.
        $post = $this->postRepository->findById(
            $postId,
            $userId
        );

        if ($post === null) {
            abort(404, 'Opslaget blev ikke fundet.');
        }
    }

}