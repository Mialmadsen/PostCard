<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Repositories\CommentRepository;
use PostCard\Repositories\PostRepository;
use PostCard\Repositories\AdminSettingsRepository;
use PostCard\Services\RateLimiter;

// Controlleren håndterer oprettelse af kommentarer til opslag.
class CommentController extends BaseController
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
        $userId = $this->requireUser();

        // Kontrollerer, om operatoren har tilladt nye kommentarer.
        $settings = (new AdminSettingsRepository())->findAll();

        if (($settings['allow_comments'] ?? '0') !== '1') {
            abort(403, 'Kommentarer er slået fra af PostCard.');
        }

        // Henter og validerer opslagets ID fra formularen.
        $postId = $this->requireInputId('post_id', 'Ugyldigt post-ID.');

        // Henter opslaget og kontrollerer, at brugeren
        // har adgang til gruppen, som opslaget tilhører.
        $post = $this->postRepository->findById(
            $postId,
            $userId
        );

        // Stopper, hvis opslaget ikke findes,
        // eller brugeren ikke har adgang til det.
        if ($post === null) {
            abort(404, 'Opslaget blev ikke fundet.');
        }

        // Henter og validerer kommentarens tekst.
        $body = $this->validateCommentBody();

        // Spam: højst 30 kommentarer i timen. Grænsen står i RateLimiter.
        $limiter = new RateLimiter();
        if ($limiter->tooMany('post_comment', $userId)) {
            abort(429, $limiter->message('post_comment'));
        }

        // Gemmer kommentaren i databasen.
        $this->commentRepository->create(
            $postId,
            $userId,
            $body
        );

        // Sender brugeren tilbage til opslagets kommentarer.
        redirect('/post?id=' . $postId . '#comments');
    }


    // Opdaterer en kommentar, som brugeren selv har skrevet.
    public function update(): void
    {
        $userId = $this->requireUser();

        // Henter og validerer kommentarens ID fra formularen.
        $commentId = $this->requireInputId('comment_id', 'Ugyldigt kommentar-ID.');

        // Henter kommentaren fra databasen.
        $comment = $this->commentRepository->findById($commentId);
        if ($comment === null) {
            abort(404, 'Kommentaren blev ikke fundet.');
        }

        // Kontrollerer, at kommentaren tilhører den indloggede bruger.
        if ((int) $comment['user_id'] !== $userId) {
            abort(403, 'Du kan kun redigere dine egne kommentarer.');
        }

        // Kontrollerer, at brugeren stadig har adgang til opslaget.
        $postId = (int) $comment['post_id'];
        if ($this->postRepository->findById($postId, $userId) === null) {
            abort(404, 'Opslaget blev ikke fundet.');
        }

        // Henter og validerer den nye kommentar.
        $body = $this->validateCommentBody();

        // Gemmer ændringerne i databasen.
        $this->commentRepository->update($commentId, $userId, $body);

        // Sender brugeren tilbage til kommentarerne.
        redirect('/post?id=' . $postId . '#comments');
    }

    // Sletter en kommentar, som brugeren selv har skrevet.
    public function delete(): void
    {
        $userId = $this->requireUser();

        // Henter og validerer kommentarens ID fra formularen.
        $commentId = $this->requireInputId('comment_id', 'Ugyldigt kommentar-ID.');

        // Henter kommentaren fra databasen.
        $comment = $this->commentRepository->findById($commentId);
        if ($comment === null) {
            abort(404, 'Kommentaren blev ikke fundet.');
        }

        // Kontrollerer, at kommentaren tilhører den indloggede bruger.
        if ((int) $comment['user_id'] !== $userId) {
            abort(403, 'Du kan kun slette dine egne kommentarer.');
        }

        // Kontrollerer, at brugeren stadig har adgang til opslaget.
        $postId = (int) $comment['post_id'];
        if ($this->postRepository->findById($postId, $userId) === null) {
            abort(404, 'Opslaget blev ikke fundet.');
        }

        // Sletter kommentaren fra databasen.
        $this->commentRepository->delete($commentId, $userId);

        // Sender brugeren tilbage til kommentarerne.
        redirect('/post?id=' . $postId . '#comments');
    }

    // Henter og validerer kommentarens tekst ved oprettelse og redigering.
    private function validateCommentBody(): string
    {
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

        return $body;
    }

}