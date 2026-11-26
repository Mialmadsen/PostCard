<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\Auth;
use PostCard\Repositories\PostImageRepository;
use PostCard\Services\CloudinaryService;

// Controlleren håndterer adgang til private billeder fra opslag.
// Før et billede må vises, kontrollerer vi brugerens gruppemedlemskab.

class PostImageController extends BaseController
{
    public function show(): void
    {
        // This request only reads the session. Release its lock at once,
        // so the page's other photos can load at the same time.
        session_write_close();

        // Kontrollerer, at brugeren er logget ind, og henter brugerens ID.
        $userId = $this->requireUser();

        // Henter og validerer billedets ID fra URL'en, fx /post-image?id=1.
        $imageId = $this->requireQueryId('id', 'Ugyldigt billed-ID.');

        // Finder billedet og gruppen, som opslaget tilhører.
        $postImageRepository = new PostImageRepository();
        $image = $postImageRepository->findImageWithGroup($imageId);

        // Stopper, hvis billedet ikke findes.
        if ($image === null) {
            abort(404, 'Billedet blev ikke fundet.');
        }

        // Operatorer kan se billeder fra alle grupper.
        // Almindelige brugere skal være aktive gruppemedlemmer.
        $users = new \PostCard\Repositories\UserRepository();
        $isOperator = $users->isOperator($userId);

        if (!$isOperator) {
            Auth::requireMember((int) $image['group_id']);
        }

        try {
            // Henter billedet fra Cloudinary på serveren.
            $cloudinary = new CloudinaryService();

            $imageData = $cloudinary->downloadImage(
                $image['public_id'],
                (int) $image['cloudinary_version'],
                'c_limit,w_1080,h_1080,q_auto'    // max 1080 px wide or high, smart quality
            );

            // Kontrollerer, at svaret faktisk er et billede.
            $detectedMime = (new \finfo(FILEINFO_MIME_TYPE))
                ->buffer($imageData);

            if ($detectedMime !== 'image/webp') {
                throw new \RuntimeException(
                    'Cloudinary returnerede ikke det forventede billede.'
                );
            }

            // Only this user's own browser may keep the photo (private), for a year.
            // The picture behind an image_id never changes, so the copy never goes stale (immutable).
            header('Cache-Control: private, max-age=31536000, immutable');
            header_remove('Pragma');      // session_start() sent "don't cache" headers by default:
            header_remove('Expires');     // remove them, so they don't contradict the line above
            header('X-Content-Type-Options: nosniff');

            // Sender billedet til browseren.
            header('Content-Type: image/webp');
            header('Content-Length: ' . strlen($imageData));

            echo $imageData;
            exit;

        } catch (\Throwable $exception) {
            // Gemmer den tekniske fejl i PHP's fejllog.
            // Viser ikke følsomme oplysninger til brugeren.
            error_log(
                'PostCard billedfejl: ' . $exception->getMessage()
            );

            abort(502, 'Billedet kunne ikke indlæses.');
        }
    }
}