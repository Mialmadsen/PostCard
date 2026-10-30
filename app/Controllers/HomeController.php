<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\Auth;
use PostCard\Core\View;
use PostCard\Repositories\GroupMemberRepository;
use PostCard\Repositories\ExperienceRepository;
use PostCard\Repositories\PostRepository;
use PostCard\Repositories\CommentRepository;
use PostCard\Repositories\UserRepository;

class HomeController
{
    // Viser forsiden for den indloggede bruger
    public function index(): void
    {
        // Kontrollerer, at brugeren er logget ind
        Auth::requireLogin();

        // Henter den indloggede brugers ID
        $userId = Auth::userId();

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
        $postRepository = new PostRepository();
        $popularPosts = $postRepository->findPopularByUser($userId);
        $stickyPosts = $postRepository->findStickyByUser($userId);

        // Gør billedadresserne klar til populære opslag
        foreach ($popularPosts as &$post) {
            // Bruger den beskyttede billedrute til Cloudinary-billeder
            if (!empty($post['image_id'])) {
                $post['image'] = url('/post-image?id=' . (int) $post['image_id']);
            } else {
                // Opslag uden billeder får ingen billedadresse
                $post['image'] = null;
            }
        }

        // Afslutter referencen fra foreach-løkken
        unset($post);

        // Gør billedadresserne klar til fastgjorte opslag
        foreach ($stickyPosts as &$post) {
            // Bruger den beskyttede billedrute til Cloudinary-billeder
            if (!empty($post['image_id'])) {
                $post['image'] = url('/post-image?id=' . (int) $post['image_id']);
            } else {
                // Opslag uden billeder får ingen billedadresse
                $post['image'] = null;
            }
        }

        // Afslutter referencen fra foreach-løkken
        unset($post);

        // Henter de nyeste kommentarer fra brugerens grupper
        $commentRepository = new CommentRepository();
        $latestComments = $commentRepository->findLatestByUser($userId);

        // Sender data videre til Home-viewet
        View::render('home', [
            'pageTitle' => 'Min forside',
            'pageStyle' => 'home',
            'isLoggedIn' => true,
            'firstName' => $firstName,
            'groups' => $groups,
            'experiences' => $experiences,
            'popularPosts' => $popularPosts,
            'stickyPosts' => $stickyPosts,
            'latestComments' => $latestComments,
        ]);
    }
}
