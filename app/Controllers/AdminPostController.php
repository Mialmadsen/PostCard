<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\Auth;
use PostCard\Core\View;
use PostCard\Repositories\AdminPostRepository;
use PostCard\Services\PostService;
use PostCard\Repositories\PostImageRepository;
use PostCard\Repositories\CommentRepository;
use PostCard\Repositories\LikeRepository;

class AdminPostController extends BaseController
{
    private AdminPostRepository $adminPostRepository;

    // Opretter repositoryet, som henter opslag fra databasen.
    public function __construct()
    {
        $this->adminPostRepository = new AdminPostRepository();
    }


    public function index(): void
    {
        // Kun operatorer kan få adgang til siden.
        Auth::requireOperator();

        // Henter søgeteksten fra URL'en, fx ?q=Esbjerg.
        $search = trim((string) ($_GET['q'] ?? ''));

        // Henter den valgte gruppe fra URL'en, fx ?group=2.
        $groupId = null;

        if (isset($_GET['group']) && $_GET['group'] !== '') {
            $groupId = filter_var(
                $_GET['group'],
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            );

            // Hvis gruppe-ID'et er ugyldigt, bruges intet gruppefilter.
            if ($groupId === false) {
                $groupId = null;
            }
        }

        // Henter opslag ud fra de valgte filtre.
        $posts = $this->adminPostRepository->findAll($search, $groupId);

        // Henter alle grupper til dropdown-menuen.
        $groups = $this->adminPostRepository->findAllGroups();

        // Sender data videre til admin-posts.php.
        View::render('admin-posts', [
            'pageTitle' => 'Administration · Opslag',
            'pageStyle' => 'admin',
            'pageScripts' => ['confirm'],
            'isLoggedIn' => true,
            'isOperator' => true,
            'adminPage' => 'posts',
            'posts' => $posts,
            'groups' => $groups,
            'search' => $search,
            'selectedGroupId' => $groupId,
        ]);
    }



    // Viser et opslag for operatoren.
    public function show(): void
    {
        Auth::requireOperator();

        $postId = $this->requireQueryId('id', 'Ugyldigt opslag-ID.');

        $post = $this->adminPostRepository->findById($postId);
        if ($post === null) {
            abort(404, 'Opslaget blev ikke fundet.');
        }

        $postImageRepository = new PostImageRepository();
        $commentRepository = new CommentRepository();
        $likeRepository = new LikeRepository();

        $images = $postImageRepository->findImagesByPost($postId);
        $comments = $commentRepository->findByPost($postId);
        $hasLiked = $likeRepository->hasLiked($postId, Auth::userId());

        View::render('post', [
            'pageTitle' => 'Administration · Vis opslag',
            'pageStyle' => 'post',
            'pageScripts' => ['lightbox', 'confirm'],
            'isLoggedIn' => true,
            'isOperator' => true,
            'operatorView' => true,
            'post' => $post,
            'images' => $images,
            'comments' => $comments,
            'hasLiked' => $hasLiked
        ]);
    }




    // Sletter et opslag som operator for hele PostCard.
    public function delete(): void
    {
        // Kun operatorer må slette opslag fra hele platformen.
        Auth::requireOperator();

        // Henter og validerer opslagets ID fra POST-formularen.
        $postId = $this->requireInputId(
            'post_id',
            'Ugyldigt opslag-ID.'
        );

        // Bruger PostService til at slette opslaget
        // og rydde op i Cloudinary.
        $postService = new PostService();

        try {
            $postService->deletePostAsOperator($postId);
        } catch (\RuntimeException $e) {
            // Stopper, hvis opslaget ikke findes
            // eller ikke kunne slettes.
            abort(404, $e->getMessage());
        }

        // Sender operatoren tilbage til opslagsoverblikket.
        redirect('/admin/posts');
    }

    // Sletter en kommentar som operator.
    public function deleteComment(): void
    {
        Auth::requireOperator();

        $commentId = $this->requireInputId(
            'comment_id',
            'Ugyldigt kommentar- eller opslag-ID.'
        );

        $postId = $this->requireInputId(
            'post_id',
            'Ugyldigt kommentar- eller opslag-ID.'
        );

        $commentRepository = new CommentRepository();
        $deleted = $commentRepository->deleteAsOperator($commentId, $postId);

        if ($deleted !== 1) {
            abort(404, 'Kommentaren blev ikke fundet.');
        }

        // Sender operatoren tilbage til opslaget.
        redirect('/admin/posts/show?id=' . $postId);
    }


}