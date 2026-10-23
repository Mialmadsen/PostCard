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
                c.body,
                c.created_at,
                p.post_id,
                p.location,
                g.name AS group_name,
                u.first_name,
                u.last_name
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
}
