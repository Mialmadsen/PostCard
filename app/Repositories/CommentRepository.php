<?php
// Filen henter kommentarer fra databasen.
// Vi bruger JOIN til at hente kommentarens forfatter,
// opslagets lokation og gruppens navn.

declare(strict_types=1);

namespace PostCard\Repositories;

class CommentRepository extends Repository
{
    // Henter de nyeste kommentarer fra brugerens grupper
    public function findLatestByUser(int $userId): array
    {
        $sql = "
            SELECT
                c.comment_id,
                c.user_id,
                c.body,
                c.created_at,
                p.post_id,
                p.location,
                g.name AS group_name,
                u.first_name,
                u.last_name,
                u.profile_image,
                u.profile_image_version
            FROM comments AS c
            INNER JOIN posts AS p ON c.post_id = p.post_id
            INNER JOIN experiences AS e ON p.experience_id = e.experience_id
            INNER JOIN user_groups AS g ON e.group_id = g.group_id
            INNER JOIN users AS u ON c.user_id = u.user_id
            INNER JOIN group_members AS gm
                ON gm.group_id = e.group_id
                AND gm.user_id = :user_id
                AND gm.member_status = 'active'
            WHERE e.experience_status IN ('approved', 'closed')
                AND u.user_status = 'active'
            ORDER BY c.created_at DESC, c.comment_id DESC
            LIMIT 3
        ";

        // Returnerer højst tre kommentarer fra brugerens grupper
        return $this->fetchAll($sql, [
            'user_id' => $userId
        ]);
    }

    // Henter alle kommentarer til et bestemt opslag.
    // JOIN bruges til at hente navnet på brugeren,
    // der har skrevet kommentaren.
    public function findByPost(int $postId): array
    {
        $sql = "
            SELECT
                c.comment_id,
                c.body,
                c.created_at,
                c.user_id,
                u.first_name,
                u.last_name
            FROM comments AS c
            INNER JOIN users AS u ON c.user_id = u.user_id
            WHERE c.post_id = :post_id
                AND u.user_status = 'active'
            ORDER BY c.created_at ASC, c.comment_id ASC
        ";

        // Returnerer alle kommentarer til opslaget.
        return $this->fetchAll($sql, [
            'post_id' => $postId
        ]);
    }

    // Gemmer en ny kommentar til et bestemt opslag.
    public function create(int $postId, int $userId, string $body): int
    {
        $sql = "
            INSERT INTO comments (post_id, user_id, body, created_at)
            VALUES (:post_id, :user_id, :body, NOW())
        ";

        // Bruger den fælles insert-metode fra Repository.
        // Returnerer ID'et på den nye kommentar.
        return $this->insert($sql, [
            'post_id' => $postId,
            'user_id' => $userId,
            'body' => $body
        ]);
    }


    // Henter en bestemt kommentar ud fra dens ID.
    public function findById(int $commentId): ?array
    {
        $sql = "
            SELECT comment_id, post_id, user_id, body
            FROM comments
            WHERE comment_id = :comment_id
        ";

        return $this->fetchOne($sql, [
            'comment_id' => $commentId
        ]);
    }

    // Opdaterer teksten i en kommentar.
    // Kun kommentarens ejer kan ændre den.
    public function update(int $commentId, int $userId, string $body): int
    {
        $sql = "
            UPDATE comments
            SET body = :body
            WHERE comment_id = :comment_id
                AND user_id = :user_id
        ";

        return $this->execute($sql, [
            'comment_id' => $commentId,
            'user_id' => $userId,
            'body' => $body
        ]);
    }

    // Sletter en kommentar fra databasen.
    // Kun kommentarens ejer kan slette den.
    public function delete(int $commentId, int $userId): int
    {
        $sql = "
            DELETE FROM comments
            WHERE comment_id = :comment_id
                AND user_id = :user_id
        ";

        return $this->execute($sql, [
            'comment_id' => $commentId,
            'user_id' => $userId
        ]);
    }
    
    // Sletter en kommentar fra et bestemt opslag som operator.
    public function deleteAsOperator(int $commentId, int $postId): int
    {
        $sql = "
            DELETE FROM comments
            WHERE comment_id = :comment_id
            AND post_id = :post_id
        ";

        return $this->execute($sql, [
            'comment_id' => $commentId,
            'post_id' => $postId
        ]);
    }


}
