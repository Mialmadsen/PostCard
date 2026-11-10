<?php

declare(strict_types=1);

namespace PostCard\Repositories;

// Repository håndterer likes på opslag i databasen.
class LikeRepository extends Repository
{
    // Kontrollerer, om en bruger allerede har liket et opslag.
    public function hasLiked(int $postId, int $userId): bool
    {
        $sql = "
            SELECT COUNT(*)
            FROM likes
            WHERE post_id = :post_id
                AND user_id = :user_id
        ";

        return (int) $this->fetchValue($sql, [
            'post_id' => $postId,
            'user_id' => $userId
        ]) > 0;
    }

    // Gemmer et like på et opslag.
    public function create(int $postId, int $userId): int
    {
        $sql = "
            INSERT INTO likes (post_id, user_id, created_at)
            VALUES (:post_id, :user_id, NOW())
        ";

        return $this->insert($sql, [
            'post_id' => $postId,
            'user_id' => $userId
        ]);
    }

    // Fjerner brugerens like fra et opslag.
    public function delete(int $postId, int $userId): int
    {
        $sql = "
            DELETE FROM likes
            WHERE post_id = :post_id
                AND user_id = :user_id
        ";

        return $this->execute($sql, [
            'post_id' => $postId,
            'user_id' => $userId
        ]);
    }
    // Tæller hvor mange likes et opslag har.
    public function countByPost(int $postId): int
    {
        $sql = "
            SELECT COUNT(*)
            FROM likes
            WHERE post_id = :post_id
        ";

        return (int) $this->fetchValue($sql, [
            'post_id' => $postId
        ]);
    }


    // Henter brugerne, der har liket et bestemt opslag.
    public function findUsersByPost(int $postId): array
    {
        $sql = "
            SELECT
                u.user_id,
                u.first_name,
                u.last_name,
                u.profile_image,
                u.profile_image_version
            FROM likes AS l
            JOIN users AS u
                ON l.user_id = u.user_id
            WHERE l.post_id = :post_id
                AND u.user_status = 'active'
            ORDER BY l.created_at DESC
        ";

        return $this->fetchAll($sql, [
            'post_id' => $postId
        ]);
    }

}
