<?php

declare(strict_types=1);

namespace PostCard\Repositories;

// Repository til at håndtere billeder, der tilhører opslag.
// Klassen arver de fælles databasefunktioner fra Repository.php.
class PostImageRepository extends Repository
{
    // Henter alle billeder, der hører til et bestemt opslag.
    public function findImagesByPost(int $postId): array
    {
        $sql = "
            SELECT
                image_id,
                post_id,
                public_id,
                cloudinary_version,
                image_format,
                alt_text,
                sort_order
            FROM post_images
            WHERE post_id = :post_id
            ORDER BY sort_order ASC, image_id ASC
        ";

        return $this->fetchAll($sql, [
            'post_id' => $postId
        ]);
    }


    // Gemmer oplysninger om et billede, der er uploadet til Cloudinary.
    public function addImage(
        int $postId,
        string $publicId,
        int $cloudinaryVersion,
        string $imageFormat,
        ?string $altText,
        int $sortOrder
    ): int {
        $sql = "
            INSERT INTO post_images (
                post_id,
                public_id,
                cloudinary_version,
                image_format,
                alt_text,
                sort_order
            )
            VALUES (
                :post_id,
                :public_id,
                :cloudinary_version,
                :image_format,
                :alt_text,
                :sort_order
            )
        ";

        return $this->insert($sql, [
            'post_id' => $postId,
            'public_id' => $publicId,
            'cloudinary_version' => $cloudinaryVersion,
            'image_format' => $imageFormat,
            'alt_text' => $altText,
            'sort_order' => $sortOrder
        ]);
    }


    // Opdaterer billedbeskrivelsen på et billede, der tilhører brugerens opslag.
    public function updateImageAltText(int $imageId, int $postId, int $userId, string $altText): int
    {
        $sql = "
            UPDATE post_images
            SET alt_text = :alt_text
            WHERE image_id = :image_id
                AND post_id = :post_id
                AND post_id IN (
                    SELECT p.post_id
                    FROM posts AS p
                    INNER JOIN experiences AS e ON p.experience_id = e.experience_id
                    WHERE e.user_id = :user_id
                )
        ";

        return $this->execute($sql, [
            'alt_text' => $altText,
            'image_id' => $imageId,
            'post_id' => $postId,
            'user_id' => $userId
        ]);
    }

    // Sletter et billede fra brugerens eget opslag.
    public function deleteImage(int $imageId, int $postId, int $userId): int
    {
        $sql = "
            DELETE FROM post_images
            WHERE image_id = :image_id
                AND post_id = :post_id
                AND post_id IN (
                    SELECT p.post_id
                    FROM posts AS p
                    INNER JOIN experiences AS e ON p.experience_id = e.experience_id
                    WHERE e.user_id = :user_id
                )
        ";

        return $this->execute($sql, [
            'image_id' => $imageId,
            'post_id' => $postId,
            'user_id' => $userId
        ]);
    }

    // Opdaterer billedets rækkefølge på brugerens eget opslag.
    public function updateImageSortOrder(int $imageId, int $postId, int $userId, int $sortOrder): int
    {
        $sql = "
            UPDATE post_images
            SET sort_order = :sort_order
            WHERE image_id = :image_id
                AND post_id = :post_id
                AND post_id IN (
                    SELECT p.post_id
                    FROM posts AS p
                    INNER JOIN experiences AS e ON p.experience_id = e.experience_id
                    WHERE e.user_id = :user_id
                )
        ";

        return $this->execute($sql, [
            'sort_order' => $sortOrder,
            'image_id' => $imageId,
            'post_id' => $postId,
            'user_id' => $userId
        ]);
    }


}
