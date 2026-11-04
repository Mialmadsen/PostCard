<?php

declare(strict_types=1);

namespace PostCard\Repositories;

// Repository til at registrere unikke visninger på opslag.
class PostViewRepository extends Repository
{
    // Registrerer en visning, hvis brugeren ikke allerede har set opslaget.
    public function registerView(int $postId, int $userId): void
    {
        $sql = "
            INSERT IGNORE INTO post_views (user_id, post_id)
            VALUES (:user_id, :post_id)
        ";

        $this->execute($sql, [
            'user_id' => $userId,
            'post_id' => $postId
        ]);
    }
}
