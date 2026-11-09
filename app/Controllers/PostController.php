<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Repositories\ExperienceRepository;
use PostCard\Repositories\PostRepository;
use PostCard\Repositories\PostImageRepository;
use PostCard\Repositories\PostFeedRepository;
use PostCard\Services\CloudinaryService;
use PostCard\Core\Auth;
use PostCard\Core\View;
use PostCard\Services\ExperienceService;
use PostCard\Services\PostService;
use PostCard\Repositories\CommentRepository;
use PostCard\Repositories\LikeRepository;
use PostCard\Repositories\PostViewRepository;

// Controlleren håndterer oprettelse af opslag.
class PostController
{
    private ExperienceRepository $experienceRepository;
    private PostRepository $postRepository;
    private PostImageRepository $postImageRepository;
    private PostFeedRepository $postFeedRepository;
    private CloudinaryService $cloudinaryService;
    private ExperienceService $experienceService;
    private CommentRepository $commentRepository;
    private LikeRepository $likeRepository;
    private PostViewRepository $postViewRepository;

    // Opretter de objekter, controlleren skal bruge.
    public function __construct()
    {
        $this->experienceRepository = new ExperienceRepository();
        $this->postRepository = new PostRepository();
        $this->postImageRepository = new PostImageRepository();
        $this->postFeedRepository = new PostFeedRepository();
        $this->cloudinaryService = new CloudinaryService();
        $this->experienceService = new ExperienceService();
        $this->commentRepository = new CommentRepository();
        $this->likeRepository = new LikeRepository();
        $this->postViewRepository = new PostViewRepository();
    }

    // Viser formularen til at oprette et nyt opslag
    public function create(): void
    {
        // Kontrollerer, at brugeren er logget ind
        Auth::requireLogin();

        // Henter oplevelsens ID fra URL'en med den fælles helper
        $experienceId = queryId('experience');

        // Stopper, hvis oplevelsens ID mangler eller er ugyldigt
        if ($experienceId === null) {
            abort(400, 'Ugyldigt experience-ID.');
        }

        // Henter oplevelsen fra databasen
        $experience = $this->experienceRepository->find($experienceId);

        // Kontrollerer, at oplevelsen findes
        if ($experience === null) {
            abort(404, 'Oplevelsen blev ikke fundet.');
        }

        // Kontrollerer, at brugeren er medlem af gruppen
        Auth::requireMember((int) $experience['group_id']);

        // Kontrollerer, at den indloggede bruger ejer oplevelsen.
        // Kun oplevelsens ejer må oprette opslag i den.
        if ((int) $experience['user_id'] !== Auth::userId()) {
            abort(403, 'Du kan kun oprette opslag i dine egne oplevelser.');
        }

        // Genbruger ExperienceService til at kontrollere,
        // om oplevelsen er godkendt og aktiv i dag.
        $status = $this->experienceService->getStatus($experience);

        // Brugeren må kun oprette opslag i aktive oplevelser.
        if (!$status['isActive']) {
            abort(403, 'Du kan ikke oprette opslag i denne oplevelse lige nu.');
        }

        // Henter alle opslag, der allerede findes i oplevelsen.
        $posts = $this->postFeedRepository->findByExperience($experienceId);

        // Tæller hvor mange opslag der er oprettet.
        $postsUsed = count($posts);

        // Genbruger ExperienceService til at beregne opslaggrænsen.
        $postCounts = $this->experienceService->getPostCounts(
            $experience,
            $postsUsed
        );

        // Kontrollerer om opslaggrænsen er nået.
        // null betyder, at gruppen ikke har nogen opslaggrænse.
        if (
            $postCounts['postsRemaining'] !== null &&
            $postCounts['postsRemaining'] <= 0
        ) {
            abort(403, 'Der er ikke flere opslag tilbage i denne oplevelse.');
        }

        // Sender oplysningerne videre til formularen
        View::render('create-post', [
            'pageTitle' => 'Nyt opslag',
            'pageStyle' => 'create-post',
            'pageScripts' => ['photo-preview'],
            'isLoggedIn' => true,
            'experience' => $experience,
            'postCounts' => $postCounts
        ]);
    }

    // Modtager formularen, når brugeren vil oprette et opslag
    public function store(): void
    {
        // Kontrollerer, at brugeren er logget ind
        Auth::requireLogin();

        // Henter oplevelsens ID fra formularen med den fælles helper
        $experienceId = inputId('experience_id');

        // Stopper, hvis oplevelsens ID mangler eller er ugyldigt
        if ($experienceId === null) {
            abort(400, 'Ugyldigt experience-ID.');
        }

        // Henter oplevelsen fra databasen
        $experience = $this->experienceRepository->find($experienceId);

        // Kontrollerer, at oplevelsen findes
        if ($experience === null) {
            abort(404, 'Oplevelsen blev ikke fundet.');
        }

        // Kontrollerer, at brugeren er aktivt medlem af gruppen
        Auth::requireMember((int) $experience['group_id']);

        // Kontrollerer, at brugeren ejer oplevelsen
        if ((int) $experience['user_id'] !== Auth::userId()) {
            abort(403, 'Du kan kun oprette opslag i dine egne oplevelser.');
        }

        // Genbruger ExperienceService til at kontrollere,
        // om oplevelsen er godkendt og aktiv i dag.
        $status = $this->experienceService->getStatus($experience);

        // Opslag må kun oprettes i aktive oplevelser.
        if (!$status['isActive']) {
            abort(403, 'Du kan ikke oprette opslag i denne oplevelse lige nu.');
        }

        // Henter de opslag, der allerede findes i oplevelsen.
        $posts = $this->postFeedRepository->findByExperience($experienceId);

        // Tæller hvor mange opslag der allerede er oprettet.
        $postsUsed = count($posts);

        // Genbruger ExperienceService til at beregne opslaggrænsen.
        $postCounts = $this->experienceService->getPostCounts(
            $experience,
            $postsUsed
        );

        // Kontrollerer, om der er plads til endnu et opslag.
        // null betyder, at gruppen ikke har nogen opslaggrænse.
        if (
            $postCounts['postsRemaining'] !== null &&
            $postCounts['postsRemaining'] <= 0
        ) {
            abort(403, 'Der er ikke flere opslag tilbage i denne oplevelse.');
        }

        // Henter tekst og lokation fra formularen.
        $caption = $_POST['caption'] ?? '';
        $location = $_POST['location'] ?? '';

        // Kontrollerer, at begge værdier er tekst.
        if (!is_string($caption) || !is_string($location)) {
            abort(400, 'Ugyldige formularoplysninger.');
        }

        // Fjerner unødvendige mellemrum før og efter teksten.
        $caption = trim($caption);
        $location = trim($location);

        // Kontrollerer tekstens maksimale længde.
        if (mb_strlen($caption) > 2000) {
            abort(400, 'Teksten må højst være 2000 tegn.');
        }

        // Lokation må højst være 150 tegn.
        if (mb_strlen($location) > 150) {
            abort(400, 'Lokationen må højst være 150 tegn.');
        }

        // Kontrollerer, at formularen indeholder uploadede billeder.
        if (
            !isset($_FILES['photos']) ||
            !is_array($_FILES['photos']['name'] ?? null)
        ) {
            abort(400, 'Du skal vælge mindst ét billede.');
        }

        // Tæller hvor mange billeder brugeren har forsøgt at uploade.
        $photoCount = count($_FILES['photos']['name']);

        // Et opslag skal indeholde mellem 1 og 5 billeder.
        if ($photoCount < 1 || $photoCount > 5) {
            abort(400, 'Du skal vælge mellem 1 og 5 billeder.');
        }

        // Samler oplysningerne om hvert billede i sit eget array.
        $photos = [];

        for ($i = 0; $i < $photoCount; $i++) {
            $photos[] = [
                'name' => $_FILES['photos']['name'][$i] ?? null,
                'type' => $_FILES['photos']['type'][$i] ?? null,
                'tmp_name' => $_FILES['photos']['tmp_name'][$i] ?? null,
                'error' => $_FILES['photos']['error'][$i] ?? null,
                'size' => $_FILES['photos']['size'][$i] ?? null
            ];
        }

        // Gennemgår alle billederne og kontrollerer, at de er gyldige.
        foreach ($photos as $photo) {
            $this->cloudinaryService->validateImage($photo);
        }

        // Henter billedbeskrivelserne fra formularen.
        $altTexts = $_POST['alt_text'] ?? [];

        // Kontrollerer, at beskrivelserne er sendt som en liste.
        if (!is_array($altTexts)) {
            abort(400, 'Ugyldige billedbeskrivelser.');
        }

        // Kontrollerer beskrivelsen til hvert billede.
        for ($i = 0; $i < $photoCount; $i++) {
            $altText = $altTexts[$i] ?? '';

            // Hver beskrivelse skal være tekst.
            if (!is_string($altText)) {
                abort(400, 'Ugyldig billedbeskrivelse.');
            }

            // Fjerner unødvendige mellemrum.
            $altText = trim($altText);

            // Databasen tillader højst 255 tegn.
            if (mb_strlen($altText) > 255) {
                abort(400, 'Billedbeskrivelsen må højst være 255 tegn.');
            }

            // Gemmer den validerede beskrivelse.
            $altTexts[$i] = $altText;
        }

        // Henter det valgte forsidebillede fra formularen.
        // JavaScript sender fx "new-0", "new-1" eller "new-2".
        $cover = $_POST['cover'] ?? 'new-0';

        // Kontrollerer, at værdien har det rigtige format.
        if (
            !is_string($cover) ||
            !preg_match('/^new-(0|[1-9][0-9]*)$/', $cover)
        ) {
            abort(400, 'Ugyldigt forsidebillede.');
        }

        // Fjerner "new-" og finder billedets nummer.
        $coverIndex = filter_var(
            substr($cover, 4),
            FILTER_VALIDATE_INT
        );

        // Kontrollerer, at billedet findes blandt de uploadede billeder.
        if (
            $coverIndex === false ||
            $coverIndex < 0 ||
            $coverIndex >= $photoCount
        ) {
            abort(400, 'Ugyldigt forsidebillede.');
        }

        // Opretter opslaget gennem PostService.
        // Servicen uploader billederne til Cloudinary
        // og gemmer opslaget og billedoplysningerne i databasen.
        $postService = new PostService();

        $postId = $postService->createPost(
            $experienceId,
            $caption,
            $location,
            $photos,
            $altTexts,
            $coverIndex
        );

        // Sender brugeren tilbage til oplevelsen,
        // hvor det nye opslag nu kan vises.
        header('Location: ' . url('/experience?id=' . $experienceId));
        exit;
    }


    // Viser et enkelt opslag med alle dets billeder og kommentarer.
    public function show(): void
    {
        // Kontrollerer, at brugeren er logget ind.
        Auth::requireLogin();

        // Henter opslagets ID fra URL'en med den fælles helper.
        $postId = queryId('id');

        // Stopper, hvis opslagets ID mangler eller er ugyldigt.
        if ($postId === null) {
            abort(400, 'Ugyldigt post-ID.');
        }

        // Henter opslaget fra databasen.
        // Repository kontrollerer også, at brugeren er aktivt
        // medlem af gruppen, som opslaget tilhører.
        $post = $this->postRepository->findById(
            $postId,
            Auth::userId()
        );

        // Stopper, hvis opslaget ikke findes, eller brugeren
        // ikke har adgang til det.
        if ($post === null) {
            abort(404, 'Opslaget blev ikke fundet.');
        }

        // Registrerer en unik visning, efter at adgang til opslaget er kontrolleret.
        $this->postViewRepository->registerView($postId, Auth::userId());

        // Henter opslaget igen, så visningstælleren er opdateret.
        $post = $this->postRepository->findById($postId, Auth::userId());

        // Henter alle billeder, der hører til opslaget.
        $images = $this->postImageRepository->findImagesByPost($postId);

        // Henter alle kommentarer til opslaget.
        $comments = $this->commentRepository->findByPost($postId);

        // Kontrollerer, om den indloggede bruger allerede har liket opslaget.
        $hasLiked = $this->likeRepository->hasLiked(
            $postId,
            Auth::userId()
        );

        // Sender opslaget, billederne og kommentarerne videre til viewet.
        // Sender opslaget, billederne, kommentarerne og like-status videre til viewet.
        View::render('post', [
            'pageTitle' => 'Opslag',
            'pageStyle' => 'post',
            'pageScripts' => ['lightbox', 'likes'],
            'isLoggedIn' => true,
            'post' => $post,
            'images' => $images,
            'comments' => $comments,
            'hasLiked' => $hasLiked
        ]);
    }


    // Viser formularen til at redigere et eksisterende opslag.
    public function edit(): void
    {
        Auth::requireLogin();

        // Henter opslagets ID fra URL'en.
        $postId = queryId('id');
        if ($postId === null) {
            abort(400, 'Ugyldigt post-ID.');
        }

        // Henter opslaget og kontrollerer gruppemedlemskab.
        $post = $this->postRepository->findById($postId, Auth::userId());
        if ($post === null) {
            abort(404, 'Opslaget blev ikke fundet.');
        }

        // Kun opslagets forfatter må redigere det.
        if ((int) $post['author_id'] !== Auth::userId()) {
            abort(403, 'Du kan kun redigere dine egne opslag.');
        }

        // Henter opslagets eksisterende billeder.
        $images = $this->postImageRepository->findImagesByPost($postId);

        // Viser redigeringsformularen med opslagets nuværende oplysninger.
        View::render('edit-post', [
            'pageTitle' => 'Rediger opslag',
            'pageStyle' => 'create-post',
            'pageScripts' => ['photo-preview'],
            'isLoggedIn' => true,
            'post' => $post,
            'images' => $images
        ]);
    }


    // Gemmer ændringerne i et eksisterende opslag.
    public function update(): void
    {
        // Kontrollerer, at brugeren er logget ind.
        Auth::requireLogin();

        // Henter opslagets ID fra formularen.
        $postId = inputId('post_id');
        if ($postId === null) {
            abort(400, 'Ugyldigt post-ID.');
        }

        // Henter opslaget og kontrollerer adgang til gruppen.
        $post = $this->postRepository->findById($postId, Auth::userId());
        if ($post === null) {
            abort(404, 'Opslaget blev ikke fundet.');
        }

        // Kun opslagets forfatter må redigere det.
        if ((int) $post['author_id'] !== Auth::userId()) {
            abort(403, 'Du kan kun redigere dine egne opslag.');
        }

        // Henter tekst og lokation fra formularen.
        $caption = $_POST['caption'] ?? '';
        $location = $_POST['location'] ?? '';

        // Kontrollerer, at begge værdier er tekst.
        if (!is_string($caption) || !is_string($location)) {
            abort(400, 'Ugyldige formularoplysninger.');
        }

        // Fjerner unødvendige mellemrum.
        $caption = trim($caption);
        $location = trim($location);

        // Samme tegnbegrænsninger som ved oprettelse af opslag.
        if (mb_strlen($caption) > 2000) {
            abort(400, 'Teksten må højst være 2000 tegn.');
        }

        if (mb_strlen($location) > 150) {
            abort(400, 'Lokationen må højst være 150 tegn.');
        }

        
        // Samler nye billeder fra formularen.
        $photos = [];
        if (isset($_FILES['photos'])) {
            if (!is_array($_FILES['photos']['name'] ?? null)) {
                abort(400, 'Ugyldige billeder.');
            }
            foreach ($_FILES['photos']['name'] as $index => $name) {
                $error = $_FILES['photos']['error'][$index] ?? UPLOAD_ERR_NO_FILE;
                if ($error === UPLOAD_ERR_NO_FILE) {
                    continue;
                }
                $photos[] = [
                    'name' => $name,
                    'type' => $_FILES['photos']['type'][$index] ?? null,
                    'tmp_name' => $_FILES['photos']['tmp_name'][$index] ?? null,
                    'error' => $error,
                    'size' => $_FILES['photos']['size'][$index] ?? null
                ];
            }
        }
        // Henter billedbeskrivelser og billeder, der skal fjernes.
        $altTexts = $_POST['alt_text'] ?? [];
        $savedAltTexts = $_POST['saved_alt_text'] ?? [];
        $removeImages = $_POST['remove_images'] ?? [];
        $cover = $_POST['cover'] ?? '';
        // Kontrollerer formularens datatyper.
        if (!is_array($altTexts) || !is_array($savedAltTexts) ||
            !is_array($removeImages) || !is_string($cover)) {
            abort(400, 'Ugyldige billedoplysninger.');
        }
        // Kontrollerer billedbeskrivelser til nye billeder.
        foreach ($altTexts as $index => $altText) {
            if (!is_string($altText) || mb_strlen(trim($altText)) > 255) {
                abort(400, 'Ugyldig billedbeskrivelse.');
            }
            $altTexts[$index] = trim($altText);
        }
        // Kontrollerer billedbeskrivelser til eksisterende billeder.
        foreach ($savedAltTexts as $imageId => $altText) {
            if (!is_string($altText) || mb_strlen(trim($altText)) > 255) {
                abort(400, 'Ugyldig billedbeskrivelse.');
            }
            $savedAltTexts[$imageId] = trim($altText);
        }
        // Gemmer tekst, lokation og billedændringer gennem PostService.
        $postService = new PostService();
        $postService->updatePostWithImages(
            $postId,
            Auth::userId(),
            $caption,
            $location,
            $photos,
            $altTexts,
            $savedAltTexts,
            $removeImages,
            $cover
        );

        // Sender brugeren tilbage til det opdaterede opslag.
        header('Location: ' . url('/post?id=' . $postId));
        exit;
    }


    // Sletter et opslag, som tilhører den indloggede bruger.
    public function delete(): void
    {
        // Kontrollerer, at brugeren er logget ind.
        Auth::requireLogin();

        // Henter opslagets ID fra formularen.
        $postId = inputId('post_id');

        if ($postId === null) {
            abort(400, 'Ugyldigt post-ID.');
        }

        // Henter opslaget og kontrollerer adgang til gruppen.
        $post = $this->postRepository->findById(
            $postId,
            Auth::userId()
        );

        if ($post === null) {
            abort(404, 'Opslaget blev ikke fundet.');
        }

        // Kun opslagets ejer må slette det.
        if ((int) $post['author_id'] !== Auth::userId()) {
            abort(403, 'Du kan kun slette dine egne opslag.');
        }

        // Gemmer oplevelsens ID, inden opslaget slettes.
        $experienceId = (int) $post['experience_id'];

        // Genbruger PostService til at slette opslaget
        // fra databasen og billederne fra Cloudinary.
        $postService = new PostService();

        $postService->deletePost(
            $postId,
            Auth::userId()
        );

        // Sender brugeren tilbage til oplevelsen.
        header('Location: ' . url('/experience?id=' . $experienceId));
        exit;
    }

}
