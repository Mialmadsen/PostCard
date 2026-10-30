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
        // Kontrollerer, at brugeren er logget ind.
        Auth::requireLogin();

        // Henter billedets ID fra URL'en, fx /post-image?id=1.
        $imageId = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        // Kontrollerer, at billedets ID er gyldigt.
        if (!$imageId || $imageId < 1) {
            http_response_code(400);
            exit('Ugyldigt billed-ID.');
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
            http_response_code(404);
            exit('Billedet blev ikke fundet.');
        }

        // Kontrollerer, at brugeren er aktivt medlem af gruppen.
        Auth::requireMember((int) $image['group_id']);

        // Bestemmer billedets MIME-type.
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp'
        ];

        $format = strtolower((string) $image['image_format']);

        if (!isset($mimeTypes[$format])) {
            http_response_code(500);
            exit('Ukendt billedformat.');
        }

        try {
            // Henter billedet fra Cloudinary på serveren.
            $cloudinary = new CloudinaryService();

            $imageData = $cloudinary->downloadImage(
                $image['public_id'],
                (int) $image['cloudinary_version'],
                $format
            );

            // Kontrollerer, at svaret faktisk er et billede.
            $detectedMime = (new \finfo(FILEINFO_MIME_TYPE))
                ->buffer($imageData);

            if ($detectedMime !== $mimeTypes[$format]) {
                throw new \RuntimeException(
                    'Cloudinary returnerede ikke det forventede billede.'
                );
            }

            // Forhindrer, at private billeder gemmes i browserens cache.
            header('Cache-Control: no-store, private');
            header('Pragma: no-cache');
            header('X-Content-Type-Options: nosniff');

            // Sender billedet til browseren.
            header('Content-Type: ' . $mimeTypes[$format]);
            header('Content-Length: ' . strlen($imageData));

            echo $imageData;
            exit;

        } catch (\Throwable $exception) {
            // Gemmer den tekniske fejl i PHP's fejllog.
            // Viser ikke følsomme oplysninger til brugeren.
            error_log(
                'PostCard billedfejl: ' . $exception->getMessage()
            );
        
            http_response_code(502);
            exit('Billedet kunne ikke indlæses.');
        }
    }
}
