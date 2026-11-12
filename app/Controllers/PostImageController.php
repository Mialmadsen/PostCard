<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\Auth;
use PostCard\Core\Database;
use PostCard\Services\CloudinaryService;
use PDO;

// Controlleren håndterer adgang til private billeder fra opslag.
// Før et billede må vises, kontrollerer vi brugerens gruppemedlemskab.
class PostImageController
{
    public function show(): void
    {
        // This request only reads the session. Release its lock at once,
        // so the page's other photos can load at the same time.
        session_write_close();

        // Kontrollerer, at brugeren er logget ind.
        Auth::requireLogin();

        // Henter billedets ID fra URL'en, fx /post-image?id=1.
        $imageId = queryId('id');

        // Kontrollerer, at billedets ID er gyldigt.
        if ($imageId === null) {
            abort(400, 'Ugyldigt billed-ID.');
        }

        // Finder billedet og gruppen, som opslaget tilhører.
        $sql = "
            SELECT
                pi.public_id,
                pi.cloudinary_version,
                pi.image_format,
                e.group_id
            FROM post_images AS pi
            INNER JOIN posts AS p
                ON pi.post_id = p.post_id
            INNER JOIN experiences AS e
                ON p.experience_id = e.experience_id
            WHERE pi.image_id = :image_id
            LIMIT 1
        ";

        $statement = Database::connection()->prepare($sql);

        $statement->execute([
            'image_id' => $imageId
        ]);

        $image = $statement->fetch(PDO::FETCH_ASSOC);

        // Stopper, hvis billedet ikke findes.
        if (!$image) {
            abort(404, 'Billedet blev ikke fundet.');
        }

        // Kontrollerer, at brugeren er aktivt medlem af gruppen.
        Auth::requireMember((int) $image['group_id']);

        try {
            // Henter billedet fra Cloudinary på serveren.
            $cloudinary = new CloudinaryService();

            $imageData = $cloudinary->downloadImage(
                $image['public_id'],
                (int) $image['cloudinary_version'],
                'c_limit,w_1080,q_auto'    // max 1080 px wide, smart quality
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
