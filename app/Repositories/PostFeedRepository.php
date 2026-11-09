<?php

declare(strict_types=1);

namespace PostCard\Repositories;

// Henter opslag til oplevelser, populære opslag og fastgjorte opslag.
class PostFeedRepository extends Repository
{
    
    // Henter alle opslag fra en bestemt oplevelse.
    // Bruger v_post_overview til oplysninger og antal interaktioner.
    public function findByExperience(int $experienceId): array
    {
        $sql = "
            SELECT
                v.post_id AS id,
                v.location,
                v.caption,
                v.created_at AS date,
                v.likes,
                v.comments,
                v.views,

                -- Henter ID'et på opslagets første billede
                (
                    SELECT pi.image_id
                    FROM post_images AS pi
                    WHERE pi.post_id = v.post_id
                    ORDER BY pi.sort_order, pi.image_id
                    LIMIT 1
                ) AS image_id,

                -- Henter Cloudinary-oplysninger
                (
                    SELECT pi.public_id
                    FROM post_images AS pi
                    WHERE pi.post_id = v.post_id
                    ORDER BY pi.sort_order, pi.image_id
                    LIMIT 1
                ) AS image_public_id,

                (
                    SELECT pi.cloudinary_version
                    FROM post_images AS pi
                    WHERE pi.post_id = v.post_id
                    ORDER BY pi.sort_order, pi.image_id
                    LIMIT 1
                ) AS image_version,

                (
                    SELECT pi.image_format
                    FROM post_images AS pi
                    WHERE pi.post_id = v.post_id
                    ORDER BY pi.sort_order, pi.image_id
                    LIMIT 1
                ) AS image_format

            FROM v_post_overview AS v

            WHERE v.experience_id = :experience_id

            ORDER BY v.created_at DESC, v.post_id DESC
        ";

        return $this->fetchAll($sql, [
            'experience_id' => $experienceId
        ]);
    }


    
    // Henter populære opslag fra grupper, hvor brugeren er aktivt medlem.
    // Opslagets oplysninger og antal likes, kommentarer og visningerkommer fra vores database-view v_post_overview.
    public function findPopularByUser(int $userId): array
    {
        $sql = "
            SELECT
                v.post_id AS id,
                v.location,
                v.caption,
                v.created_at AS date,
                v.group_name,
                v.first_name,
                v.last_name,
                v.likes,
                v.comments,
                v.views,

                -- Henter ID'et på opslagets første billede
                (
                    SELECT pi.image_id
                    FROM post_images AS pi
                    WHERE pi.post_id = v.post_id
                    ORDER BY pi.sort_order, pi.image_id
                    LIMIT 1
                ) AS image_id,

                -- Henter Cloudinary-oplysninger
                (
                    SELECT pi.public_id
                    FROM post_images AS pi
                    WHERE pi.post_id = v.post_id
                    ORDER BY pi.sort_order, pi.image_id
                    LIMIT 1
                ) AS image_public_id,

                (
                    SELECT pi.cloudinary_version
                    FROM post_images AS pi
                    WHERE pi.post_id = v.post_id
                    ORDER BY pi.sort_order, pi.image_id
                    LIMIT 1
                ) AS image_version,

                (
                    SELECT pi.image_format
                    FROM post_images AS pi
                    WHERE pi.post_id = v.post_id
                    ORDER BY pi.sort_order, pi.image_id
                    LIMIT 1
                ) AS image_format

            FROM v_post_overview AS v

            -- Kontrollerer, at brugeren er aktivt medlem af gruppen
            INNER JOIN group_members AS gm
                ON gm.group_id = v.group_id
                AND gm.user_id = :user_id
                AND gm.member_status = 'active'

            -- Kun opslag fra de seneste 7 dage og godkendte/afsluttede oplevelser
            WHERE v.created_at >= NOW() - INTERVAL 7 DAY
            AND v.experience_status IN ('approved', 'closed')

            -- Sorterer efter flest likes og derefter nyeste opslag
            ORDER BY v.likes DESC, v.created_at DESC
            LIMIT 3
        ";

        return $this->fetchAll($sql, [
            'user_id' => $userId
        ]);
    }


    
    // Henter fastgjorte opslag fra grupper, hvor brugeren er aktivt medlem.
    // Bruger v_post_overview til opslagets oplysninger og tællinger.
    public function findStickyByUser(int $userId): array
    {
        $sql = "
            SELECT
                v.post_id AS id,
                v.location,
                v.caption,
                v.created_at AS date,
                v.group_name,
                v.first_name,
                v.last_name,
                v.likes,
                v.comments,
                v.views,

                (
                    SELECT pi.image_id
                    FROM post_images AS pi
                    WHERE pi.post_id = v.post_id
                    ORDER BY pi.sort_order, pi.image_id
                    LIMIT 1
                ) AS image_id,

                (
                    SELECT pi.public_id
                    FROM post_images AS pi
                    WHERE pi.post_id = v.post_id
                    ORDER BY pi.sort_order, pi.image_id
                    LIMIT 1
                ) AS image_public_id,

                (
                    SELECT pi.cloudinary_version
                    FROM post_images AS pi
                    WHERE pi.post_id = v.post_id
                    ORDER BY pi.sort_order, pi.image_id
                    LIMIT 1
                ) AS image_version,

                (
                    SELECT pi.image_format
                    FROM post_images AS pi
                    WHERE pi.post_id = v.post_id
                    ORDER BY pi.sort_order, pi.image_id
                    LIMIT 1
                ) AS image_format

            FROM v_post_overview AS v

            INNER JOIN posts AS p
                ON p.post_id = v.post_id

            INNER JOIN group_members AS gm
                ON gm.group_id = v.group_id
                AND gm.user_id = :user_id
                AND gm.member_status = 'active'

            WHERE p.is_sticky = 1
            AND v.experience_status IN ('approved', 'closed')

            ORDER BY v.created_at DESC
            LIMIT 3
        ";

        return $this->fetchAll($sql, [
            'user_id' => $userId
        ]);
    }

}
