<?php

declare(strict_types=1);

namespace PostCard\Services;

use PostCard\Repositories\PostRepository;

// Service til at håndtere oprettelse af opslag.
// Samler arbejdet mellem databasen og Cloudinary.
class PostService
{
    private PostRepository $postRepository;
    private CloudinaryService $cloudinaryService;

    // Opretter de objekter, som servicen skal bruge.
    public function __construct()
    {
        $this->postRepository = new PostRepository();
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
}
