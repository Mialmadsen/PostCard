<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\Auth;
use PostCard\Repositories\LikeRepository;
use PostCard\Repositories\PostRepository;

// Controlleren håndterer likes på opslag.
class LikeController
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
        Auth::requireLogin();

        // Henter opslagets ID fra formularen.
        $postId = inputId('post_id');

        // Stopper, hvis opslagets ID mangler eller er ugyldigt.
        if ($postId === null) {
            abort(400, 'Ugyldigt post-ID.');
        }

        // Henter opslaget og kontrollerer, at brugeren
        // har adgang til gruppen, som opslaget tilhører.
        $post = $this->postRepository->findById(
            $postId,
            Auth::userId()
        );

        // Stopper, hvis opslaget ikke findes,
        // eller brugeren ikke har adgang til det.
        if ($post === null) {
            abort(404, 'Opslaget blev ikke fundet.');
        }

        // Kontrollerer, om brugeren allerede har liket opslaget.
        $hasLiked = $this->likeRepository->hasLiked(
            $postId,
            Auth::userId()
        );

        if ($hasLiked) {
            // Fjerner liket, hvis brugeren allerede har liket opslaget.
            $this->likeRepository->delete($postId, Auth::userId());
        } else {
            // Gemmer et like, hvis brugeren ikke har liket opslaget.
            $this->likeRepository->create($postId, Auth::userId());
        }

        // Hvis anmodningen kommer fra JavaScript,
        // sender vi resultatet som JSON i stedet for at genindlæse siden.
        if ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '' === 'XMLHttpRequest') {
            header('Content-Type: application/json; charset=utf-8');

            echo json_encode([
                'success' => true,
                'liked' => !$hasLiked,
                'likes' => $this->likeRepository->countByPost($postId)
            ]);

            exit;
        }

        // Almindelig formular virker stadig uden JavaScript.
        header('Location: ' . url('/post?id=' . $postId));
        exit;
    }
}
