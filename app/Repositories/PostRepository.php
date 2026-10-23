<?php
// Filen henter opslag fra databasen.
// Vi bruger SQL-queries med JOIN og subqueries til at hente
// opslag, billeder, likes, kommentarer og visninger.

declare(strict_types=1);

namespace PostCard\Repositories;

// Klassen henter opslag fra databasen.
// extends Repository betyder, at klassen arver de fælles
// databasefunktioner fra vores parent-klasse Repository.php.
class PostRepository extends Repository
{
    // Henter alle opslag, der hører til en bestemt oplevelse
    public function findByExperience(int $experienceId): array
    {
        $sql = "
            SELECT
                p.post_id AS id,
                p.location,
                p.caption,
                p.created_at AS date,

                -- Henter det første billede til opslaget
                (
                    SELECT pi.image_url
                    FROM post_images AS pi
                    WHERE pi.post_id = p.post_id
                    ORDER BY pi.sort_order, pi.image_id
                    LIMIT 1
                ) AS image,

                -- Tæller hvor mange likes opslaget har
                (
                    SELECT COUNT(*)
                    FROM likes AS l
                    WHERE l.post_id = p.post_id
                ) AS likes,

                -- Tæller hvor mange kommentarer opslaget har
                (
                    SELECT COUNT(*)
                    FROM comments AS c
                    WHERE c.post_id = p.post_id
                ) AS comments,

                -- Tæller hvor mange visninger opslaget har
                (
                    SELECT COUNT(*)
                    FROM post_views AS pv
                    WHERE pv.post_id = p.post_id
                ) AS views

            FROM posts AS p
            WHERE p.experience_id = :experience_id
            ORDER BY p.created_at DESC, p.post_id DESC
        ";

        // Returnerer alle opslag fra den valgte oplevelse
        return $this->fetchAll($sql, [
            'experience_id' => $experienceId
        ]);
    }

    // Henter populære opslag fra den indloggede brugers grupper
    public function findPopularByUser(int $userId): array
    {
        $sql = "
            SELECT
                p.post_id AS id,
                p.location,
                p.caption,
                p.created_at AS date,
                g.name AS group_name,
                u.first_name,
                u.last_name,

                -- Henter det første billede til opslaget
                (
                    SELECT pi.image_url
                    FROM post_images AS pi
                    WHERE pi.post_id = p.post_id
                    ORDER BY pi.sort_order, pi.image_id
                    LIMIT 1
                ) AS image,

                -- Tæller antal likes
                (
                    SELECT COUNT(*)
                    FROM likes AS l
                    WHERE l.post_id = p.post_id
                ) AS likes,

                -- Tæller antal kommentarer
                (
                    SELECT COUNT(*)
                    FROM comments AS c
                    WHERE c.post_id = p.post_id
                ) AS comments,

                -- Tæller antal visninger
                (
                    SELECT COUNT(*)
                    FROM post_views AS pv
                    WHERE pv.post_id = p.post_id
                ) AS views

            FROM posts AS p
            INNER JOIN experiences AS e ON p.experience_id = e.experience_id
            INNER JOIN user_groups AS g ON e.group_id = g.group_id
            INNER JOIN users AS u ON e.user_id = u.user_id
            INNER JOIN group_members AS gm
                ON gm.group_id = e.group_id
                AND gm.user_id = :user_id
                AND gm.member_status = 'active'

            -- Henter opslag fra de seneste 7 dage
            -- og kun fra godkendte eller afsluttede oplevelser
            WHERE p.created_at >= NOW() - INTERVAL 7 DAY
                AND e.experience_status IN ('approved', 'closed')

            -- Sorterer efter flest likes og derefter nyeste opslag
            ORDER BY likes DESC, p.created_at DESC
            LIMIT 3
        ";

        // Returnerer højst tre populære opslag fra brugerens grupper
        return $this->fetchAll($sql, [
            'user_id' => $userId
        ]);
    }

    // Henter fastgjorte opslag fra grupper, hvor brugeren er aktivt medlem
    public function findStickyByUser(int $userId): array
    {
        $sql = "
            SELECT
                p.post_id AS id,
                p.location,
                p.caption,
                p.created_at AS date,
                g.name AS group_name,
                u.first_name,
                u.last_name,

                -- Henter det første billede til opslaget
                (
                    SELECT pi.image_url
                    FROM post_images AS pi
                    WHERE pi.post_id = p.post_id
                    ORDER BY pi.sort_order, pi.image_id
                    LIMIT 1
                ) AS image,

                -- Tæller antal likes
                (
                    SELECT COUNT(*)
                    FROM likes AS l
                    WHERE l.post_id = p.post_id
                ) AS likes,

                -- Tæller antal kommentarer
                (
                    SELECT COUNT(*)
                    FROM comments AS c
                    WHERE c.post_id = p.post_id
                ) AS comments,

                -- Tæller antal visninger
                (
                    SELECT COUNT(*)
                    FROM post_views AS pv
                    WHERE pv.post_id = p.post_id
                ) AS views

            FROM posts AS p
            INNER JOIN experiences AS e ON p.experience_id = e.experience_id
            INNER JOIN user_groups AS g ON e.group_id = g.group_id
            INNER JOIN users AS u ON e.user_id = u.user_id
            INNER JOIN group_members AS gm
                ON gm.group_id = e.group_id
                AND gm.user_id = :user_id
                AND gm.member_status = 'active'

            -- Henter kun fastgjorte opslag fra godkendte eller afsluttede oplevelser
            WHERE p.is_sticky = 1
                AND e.experience_status IN ('approved', 'closed')

            -- Viser de nyeste fastgjorte opslag først
            ORDER BY p.created_at DESC
            LIMIT 3
        ";

        // Returnerer højst tre fastgjorte opslag fra brugerens grupper
        return $this->fetchAll($sql, [
            'user_id' => $userId
        ]);
    }
}
