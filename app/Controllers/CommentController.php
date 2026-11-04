<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\Auth;
use PostCard\Repositories\CommentRepository;
use PostCard\Repositories\PostRepository;

// Controlleren håndterer oprettelse af kommentarer til opslag.
class CommentController
{
    private CommentRepository $commentRepository;
    private PostRepository $postRepository;

    // Opretter de repositories, controlleren skal bruge.
    public function __construct()
    {
        $this->commentRepository = new CommentRepository();
        $this->postRepository = new PostRepository();
    }

    // Modtager og gemmer en ny kommentar til et opslag.
    public function store(): void
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

        // Henter kommentarens tekst fra formularen.
        $body = $_POST['body'] ?? '';

        // Kontrollerer, at kommentaren er tekst.
        if (!is_string($body)) {
            abort(400, 'Ugyldig kommentar.');
        }

        // Fjerner mellemrum før og efter kommentaren.
        $body = trim($body);

        // Kontrollerer, at kommentaren ikke er tom.
        if ($body === '') {
            abort(400, 'Kommentaren må ikke være tom.');
        }

        // Begrænser kommentarens længde til 2000 tegn.
        if (mb_strlen($body) > 2000) {
            abort(400, 'Kommentaren må højst være 2000 tegn.');
        }

        // Gemmer kommentaren i databasen.
        $this->commentRepository->create(
            $postId,
            Auth::userId(),
            $body
        );

        // Sender brugeren tilbage til opslagets kommentarer.
        header('Location: ' . url('/post?id=' . $postId) . '#comments');
        exit;
    }


    // Opdaterer en kommentar, som brugeren selv har skrevet.
    public function update(): void
    {
        Auth::requireLogin();

        // Henter kommentarens ID fra formularen.
        $commentId = inputId('comment_id');
        if ($commentId === null) {
            abort(400, 'Ugyldigt kommentar-ID.');
        }

        // Henter kommentaren fra databasen.
        $comment = $this->commentRepository->findById($commentId);
        if ($comment === null) {
            abort(404, 'Kommentaren blev ikke fundet.');
        }

        // Kontrollerer, at kommentaren tilhører den indloggede bruger.
        if ((int) $comment['user_id'] !== Auth::userId()) {
            abort(403, 'Du kan kun redigere dine egne kommentarer.');
        }

        // Kontrollerer, at brugeren stadig har adgang til opslaget.
        $postId = (int) $comment['post_id'];
        if ($this->postRepository->findById($postId, Auth::userId()) === null) {
            abort(404, 'Opslaget blev ikke fundet.');
        }

        // Henter og validerer den nye kommentar.
        $body = $_POST['body'] ?? '';
        if (!is_string($body)) {
            abort(400, 'Ugyldig kommentar.');
        }

        $body = trim($body);
        if ($body === '') {
            abort(400, 'Kommentaren må ikke være tom.');
        }

        if (mb_strlen($body) > 2000) {
            abort(400, 'Kommentaren må højst være 2000 tegn.');
        }

        // Gemmer ændringerne i databasen.
        $this->commentRepository->update($commentId, Auth::userId(), $body);

        // Sender brugeren tilbage til kommentarerne.
        header('Location: ' . url('/post?id=' . $postId) . '#comments');
        exit;
    }

    // Sletter en kommentar, som brugeren selv har skrevet.
    public function delete(): void
    {
        Auth::requireLogin();

        // Henter kommentarens ID fra formularen.
        $commentId = inputId('comment_id');
        if ($commentId === null) {
            abort(400, 'Ugyldigt kommentar-ID.');
        }

        // Henter kommentaren fra databasen.
        $comment = $this->commentRepository->findById($commentId);
        if ($comment === null) {
            abort(404, 'Kommentaren blev ikke fundet.');
        }

        // Kontrollerer, at kommentaren tilhører den indloggede bruger.
        if ((int) $comment['user_id'] !== Auth::userId()) {
            abort(403, 'Du kan kun slette dine egne kommentarer.');
        }

        // Kontrollerer, at brugeren stadig har adgang til opslaget.
        $postId = (int) $comment['post_id'];
        if ($this->postRepository->findById($postId, Auth::userId()) === null) {
            abort(404, 'Opslaget blev ikke fundet.');
        }

        // Sletter kommentaren fra databasen.
        $this->commentRepository->delete($commentId, Auth::userId());

        // Sender brugeren tilbage til kommentarerne.
        header('Location: ' . url('/post?id=' . $postId) . '#comments');
        exit;
    }

}
