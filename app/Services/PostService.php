<?php
declare(strict_types=1);

namespace PostCard\Services;

use PostCard\Repositories\PostRepository;
use PostCard\Repositories\PostImageRepository;
use PostCard\Core\Auth;
use PostCard\Repositories\AdminPostRepository;

// Service til at håndtere oprettelse og sletning af opslag.
// // Samler arbejdet mellem databasen og Cloudinary.
class PostService
{
    private PostRepository $postRepository;
    private PostImageRepository $postImageRepository;
    private CloudinaryService $cloudinaryService;

    // Opretter de objekter, som servicen skal bruge.
    public function __construct()
    {
        $this->postRepository = new PostRepository();
        $this->postImageRepository = new PostImageRepository();
        $this->cloudinaryService = new CloudinaryService();
    }

    // Opretter et opslag med tilhørende billeder.
    public function createPost(
        int $experienceId,
        string $caption,
        string $location,
        array $photos,
        array $altTexts,
        int $coverIndex
    ): int {
        // Billedernes oplysninger samles her efter upload.
        $uploadedImages = [];

        try {
            // Uploader billederne ét ad gangen til Cloudinary.
            foreach ($photos as $index => $photo) {
                $uploadedImage = $this->cloudinaryService->upload($photo);

                // Gemmer oplysningerne til senere brug i databasen.
                $uploadedImages[] = [
                    'public_id' => $uploadedImage['public_id'],
                    'version' => $uploadedImage['version'],
                    'format' => $uploadedImage['format'],
                    'alt_text' => $altTexts[$index] ?? null
                ];
            }

            // Kontrollerer, at det valgte forsidebillede findes.
            if (
                $coverIndex < 0 ||
                $coverIndex >= count($uploadedImages)
            ) {
                throw new \InvalidArgumentException(
                    'Det valgte forsidebillede er ugyldigt.'
                );
            }

            // Flytter forsidebilledet til starten af listen.
            // PostRepository bruger rækkefølgen til sort_order.
            $coverImage = $uploadedImages[$coverIndex];

            array_splice($uploadedImages, $coverIndex, 1);

            array_unshift($uploadedImages, $coverImage);

            // Gemmer opslaget og billederne i databasen.
            // Repository bruger en transaktion, så alle
            // databaseændringer rulles tilbage ved fejl.
            $postId = $this->postRepository->createWithImages(
                $experienceId,
                $caption,
                $location,
                $uploadedImages
            );

            // Returnerer ID'et på det nye opslag.
            return $postId;

        } catch (\Throwable $e) {
            // Forsøger at slette billeder, som allerede er uploadet.
            foreach ($uploadedImages as $image) {
                try {
                    $this->cloudinaryService->delete($image['public_id']);
                } catch (\Throwable $cleanupError) {
                    // En fejl under oprydningen må ikke skjule den oprindelige fejl.
                    // Oprydningsfejlen logges på serveren.
                    error_log(
                        'Cloudinary-oprydning mislykkedes: ' .
                        $cleanupError->getMessage()
                    );
                }
            }

            // Sender den oprindelige fejl videre.
            throw $e;
        }
    }


    // Sletter et opslag og rydder op i dets billeder på Cloudinary.
    public function deletePost(int $postId, int $userId): void
    {
        // Henter opslaget og kontrollerer, at brugeren har adgang.
        $post = $this->postRepository->findById($postId, $userId);

        if ($post === null) {
            throw new \RuntimeException('Opslaget blev ikke fundet.');
        }

        // Kontrollerer, at brugeren ejer opslaget.
        if ((int) $post['author_id'] !== $userId) {
            throw new \RuntimeException('Du kan kun slette dine egne opslag.');
        }

        // Henter billedernes Cloudinary-oplysninger inden sletningen.
        $images = $this->postImageRepository->findImagesByPost($postId);

        // Sletter opslaget fra databasen.
        // CASCADE sletter kommentarer, likes, visninger og billedoplysninger.
        $deleted = $this->postRepository->delete($postId, $userId);

        if ($deleted !== 1) {
            throw new \RuntimeException('Opslaget kunne ikke slettes.');
        }

        // Sletter derefter billederne fra Cloudinary.
        foreach ($images as $image) {
            try {
                $this->cloudinaryService->delete($image['public_id']);
            } catch (\Throwable $e) {
                // Logger fejlen, så vi kan rydde op senere.
                // Selve opslaget er allerede slettet fra databasen.
                error_log(
                    'Cloudinary-sletning mislykkedes for ' .
                    $image['public_id'] . ': ' .
                    $e->getMessage()
                );
            }
        }
    }


    // Sletter et opslag som operator på tværs af hele PostCard.
    public function deletePostAsOperator(int $postId): void
    {
        // Kontrollerer, at brugeren er operator for hele hjemmesiden.
        Auth::requireOperator();

        // Repository med adgang til opslag fra alle grupper.
        $adminPostRepository = new AdminPostRepository();

        // Kontrollerer, at opslaget findes.
        $post = $adminPostRepository->findById($postId);

        if ($post === null) {
            throw new \RuntimeException('Opslaget blev ikke fundet.');
        }

        // Henter Cloudinary-oplysninger, inden opslaget slettes.
        $images = $this->postImageRepository->findImagesByPost($postId);

        // Sletter opslaget fra databasen.
        // ON DELETE CASCADE rydder op i relaterede databaseposter.
        $deleted = $adminPostRepository->delete($postId);

        if ($deleted !== 1) {
            throw new \RuntimeException('Opslaget kunne ikke slettes.');
        }

        // Sletter billederne fra Cloudinary efter databasesletningen.
        foreach ($images as $image) {
            try {
                $this->cloudinaryService->delete($image['public_id']);
            } catch (\Throwable $e) {
                // Logger fejlen, hvis et billede ikke kunne slettes.
                // Opslaget er allerede slettet fra databasen.
                error_log(
                    'Operator: Cloudinary-sletning mislykkedes for ' .
                    $image['public_id'] . ': ' .
                    $e->getMessage()
                );
            }
        }
    }



    // Forbereder billedændringer på et eksisterende opslag.
    public function updatePostWithImages(
        int $postId,
        int $userId,
        string $caption,
        string $location,
        array $photos,
        array $altTexts,
        array $savedAltTexts,
        array $removeImages,
        string $cover
    ): void {
        // Kontrollerer, at opslaget findes og tilhører brugeren.
        $post = $this->postRepository->findById($postId, $userId);
        if ($post === null) {
            throw new \RuntimeException('Opslaget blev ikke fundet.');
        }
        if ((int) $post['author_id'] !== $userId) {
            throw new \RuntimeException('Du kan kun redigere dine egne opslag.');
        }
        // Henter de eksisterende billeder.
        $existingImages = $this->postImageRepository->findImagesByPost($postId);
        $existingById = [];
        foreach ($existingImages as $image) {
            $existingById[(int) $image['image_id']] = $image;
        }
        // Kontrollerer, at billederne, der skal fjernes, tilhører opslaget.
        $removeIds = [];
        foreach ($removeImages as $imageId) {
            $imageId = filter_var($imageId, FILTER_VALIDATE_INT);
            if ($imageId === false || !isset($existingById[$imageId])) {
                throw new \InvalidArgumentException('Ugyldigt billede valgt til sletning.');
            }
            $removeIds[$imageId] = true;
        }
        // Finder de billeder, der skal beholdes.
        $keptImages = array_filter(
            $existingImages,
            fn($image) => !isset($removeIds[(int) $image['image_id']])
        );
        // Kontrollerer, at opslaget har mellem 1 og 5 billeder efter ændringen.
        $totalImages = count($keptImages) + count($photos);
        if ($totalImages < 1 || $totalImages > 5) {
            throw new \InvalidArgumentException('Et opslag skal have mellem 1 og 5 billeder.');
        }
        // Kontrollerer alt-teksterne.
        foreach ($savedAltTexts as $imageId => $altText) {
            if (!isset($existingById[(int) $imageId]) || !is_string($altText) || mb_strlen($altText) > 255) {
                throw new \InvalidArgumentException('Ugyldig billedbeskrivelse.');
            }
        }
        foreach ($altTexts as $altText) {
            if (!is_string($altText) || mb_strlen($altText) > 255) {
                throw new \InvalidArgumentException('Ugyldig billedbeskrivelse.');
            }
        }
        // Kontrollerer, at det valgte forsidebillede findes.
        if (preg_match('/^saved-([1-9][0-9]*)$/', $cover, $matches)) {
            if (!isset($existingById[(int) $matches[1]]) || isset($removeIds[(int) $matches[1]])) {
                throw new \InvalidArgumentException('Ugyldigt forsidebillede.');
            }
        } elseif (preg_match('/^new-(0|[1-9][0-9]*)$/', $cover, $matches)) {
            if ((int) $matches[1] >= count($photos)) {
                throw new \InvalidArgumentException('Ugyldigt forsidebillede.');
            }
        } else {
            throw new \InvalidArgumentException('Ugyldigt forsidebillede.');
        }
        // Uploader nye billeder til Cloudinary.
        $uploadedImages = [];
        try {
            foreach ($photos as $index => $photo) {
                $this->cloudinaryService->validateImage($photo);
                $uploaded = $this->cloudinaryService->upload($photo);
                $uploadedImages[] = [
                    'public_id' => $uploaded['public_id'],
                    'version' => $uploaded['version'],
                    'format' => $uploaded['format'],
                    'alt_text' => $altTexts[$index] ?? ''
                ];
            }
            // Gemmer opslaget og alle billedændringer samlet i databasen.
            $this->postRepository->saveImageChanges(
                $postId,
                $userId,
                $caption,
                $location,
                $keptImages,
                $removeIds,
                $savedAltTexts,
                $uploadedImages,
                $cover
            );

        } catch (\Throwable $e) {
            // Fjerner nye uploads, hvis noget fejler.
            foreach ($uploadedImages as $image) {
                try {
                    $this->cloudinaryService->delete($image['public_id']);
                } catch (\Throwable $cleanupError) {
                    error_log('Cloudinary-oprydning mislykkedes for ' . $image['public_id'] . ': ' . $cleanupError->getMessage());
                }
            }
            throw $e;
        }

        // Sletter de fjernede billeder fra Cloudinary efter databaseændringerne.
        foreach (array_keys($removeIds) as $imageId) {
            try {
                $this->cloudinaryService->delete($existingById[$imageId]['public_id']);
            } catch (\Throwable $e) {
                // Logger fejlen, så billedet kan ryddes op senere.
                error_log(
                    'Cloudinary-sletning mislykkedes for ' .
                    $existingById[$imageId]['public_id'] . ': ' .
                    $e->getMessage()
                );
            }
        }
    }

}
