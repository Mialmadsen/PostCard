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

                -- Henter ID'et på opslagets første billede
                (
                    SELECT pi.image_id
                    FROM post_images AS pi
                    WHERE pi.post_id = p.post_id
                    ORDER BY pi.sort_order, pi.image_id
                    LIMIT 1
                ) AS image_id,

                -- Henter Cloudinary-oplysninger for opslagets første billede
                (
                    SELECT pi.public_id
                    FROM post_images AS pi
                    WHERE pi.post_id = p.post_id
                    ORDER BY pi.sort_order, pi.image_id
                    LIMIT 1
                ) AS image_public_id,

                (
                    SELECT pi.cloudinary_version
                    FROM post_images AS pi
                    WHERE pi.post_id = p.post_id
                    ORDER BY pi.sort_order, pi.image_id
                    LIMIT 1
                ) AS image_version,

                (
                    SELECT pi.image_format
                    FROM post_images AS pi
                    WHERE pi.post_id = p.post_id
                    ORDER BY pi.sort_order, pi.image_id
                    LIMIT 1
                ) AS image_format,

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

                -- Henter ID'et på opslagets første billede
                (
                    SELECT pi.image_id
                    FROM post_images AS pi
                    WHERE pi.post_id = p.post_id
                    ORDER BY pi.sort_order, pi.image_id
                    LIMIT 1
                ) AS image_id,

                -- Henter Cloudinary-oplysninger for opslagets første billede
                (
                    SELECT pi.public_id
                    FROM post_images AS pi
                    WHERE pi.post_id = p.post_id
                    ORDER BY pi.sort_order, pi.image_id
                    LIMIT 1
                ) AS image_public_id,

                (
                    SELECT pi.cloudinary_version
                    FROM post_images AS pi
                    WHERE pi.post_id = p.post_id
                    ORDER BY pi.sort_order, pi.image_id
                    LIMIT 1
                ) AS image_version,

                (
                    SELECT pi.image_format
                    FROM post_images AS pi
                    WHERE pi.post_id = p.post_id
                    ORDER BY pi.sort_order, pi.image_id
                    LIMIT 1
                ) AS image_format,

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

                -- Henter ID'et på opslagets første billede
                (
                    SELECT pi.image_id
                    FROM post_images AS pi
                    WHERE pi.post_id = p.post_id
                    ORDER BY pi.sort_order, pi.image_id
                    LIMIT 1
                ) AS image_id,

                -- Henter Cloudinary-oplysninger for opslagets første billede
                (
                    SELECT pi.public_id
                    FROM post_images AS pi
                    WHERE pi.post_id = p.post_id
                    ORDER BY pi.sort_order, pi.image_id
                    LIMIT 1
                ) AS image_public_id,

                (
                    SELECT pi.cloudinary_version
                    FROM post_images AS pi
                    WHERE pi.post_id = p.post_id
                    ORDER BY pi.sort_order, pi.image_id
                    LIMIT 1
                ) AS image_version,

                (
                    SELECT pi.image_format
                    FROM post_images AS pi
                    WHERE pi.post_id = p.post_id
                    ORDER BY pi.sort_order, pi.image_id
                    LIMIT 1
                ) AS image_format,

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


    // Opretter et nyt opslag i databasen
    public function create(
        int $experienceId,
        string $caption,
        string $location
    ): int {
        // SQL-query til at indsætte opslaget i posts-tabellen
        $sql = "
            INSERT INTO posts (experience_id, caption, location)
            VALUES (:experience_id, :caption, :location)
        ";

        // Genbruger insert() fra den fælles Repository-klasse.
        // Returnerer ID'et på det nye opslag.
        return $this->insert($sql, [
            'experience_id' => $experienceId,
            'caption' => $caption,
            'location' => $location
        ]);
    }


    // Gemmer oplysninger om et billede, der er uploadet til Cloudinary
    public function addImage(
        int $postId,
        string $publicId,
        int $cloudinaryVersion,
        string $imageFormat,
        ?string $altText,
        int $sortOrder
    ): int {
        // SQL-query til at knytte Cloudinary-billedet til et opslag
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

        // Genbruger insert() fra vores fælles Repository-klasse
        return $this->insert($sql, [
            'post_id' => $postId,
            'public_id' => $publicId,
            'cloudinary_version' => $cloudinaryVersion,
            'image_format' => $imageFormat,
            'alt_text' => $altText,
            'sort_order' => $sortOrder
        ]);
    }


    // Opretter et opslag og gemmer alle billeder i én transaktion.
    // Hvis noget fejler, bliver ændringerne i databasen rullet tilbage.
    public function createWithImages(
        int $experienceId,
        string $caption,
        string $location,
        array $images
    ): int {
        // Genbruger transaction() fra den fælles Repository-klasse.
        return $this->transaction(function () use (
            $experienceId,
            $caption,
            $location,
            $images
        ): int {
            // Låser oplevelsen, mens opslaget bliver oprettet.
            // Andre oprettelser i samme oplevelse må vente.
            $experience = $this->fetchOne(
                "
                SELECT
                    e.experience_id,
                    e.user_id,
                    e.group_id,
                    e.experience_status,
                    e.start_date,
                    e.end_date,
                    e.post_limit,
                    g.posting_mode
                FROM experiences AS e
                INNER JOIN user_groups AS g
                    ON e.group_id = g.group_id
                WHERE e.experience_id = :experience_id
                FOR UPDATE
                ",
                ['experience_id' => $experienceId]
            );

            // Kontrollerer, at oplevelsen stadig findes.
            if ($experience === null) {
                throw new \RuntimeException(
                    'Oplevelsen blev ikke fundet.'
                );
            }

            // Kontrollerer, at den indloggede bruger ejer oplevelsen.
            if ((int) $experience['user_id'] !== \PostCard\Core\Auth::userId()) {
                throw new \RuntimeException(
                    'Du kan kun oprette opslag i dine egne oplevelser.'
                );
            }

            // Kontrollerer, at brugeren stadig er aktivt medlem af gruppen.
            $isMember = $this->fetchValue(
                "
                SELECT 1
                FROM group_members
                WHERE user_id = :user_id
                    AND group_id = :group_id
                    AND member_status = 'active'
                LIMIT 1
                ",
                [
                    'user_id' => \PostCard\Core\Auth::userId(),
                    'group_id' => $experience['group_id']
                ]
            );

            if (!$isMember) {
                throw new \RuntimeException(
                    'Du har ikke adgang til denne gruppe.'
                );
            }

            // Kontrollerer, at oplevelsen er godkendt og aktiv i dag.
            $experienceService = new \PostCard\Services\ExperienceService();
            $status = $experienceService->getStatus($experience);

            if (!$status['isActive']) {
                throw new \RuntimeException(
                    'Du kan ikke oprette opslag i denne oplevelse lige nu.'
                );
            }

            // Tæller opslagene, mens oplevelsen er låst.
            $postsUsed = (int) $this->fetchValue(
                "
                SELECT COUNT(*)
                FROM posts
                WHERE experience_id = :experience_id
                ",
                ['experience_id' => $experienceId]
            );

            // Genbruger ExperienceService til at beregne opslaggrænsen.
            $postCounts = $experienceService->getPostCounts(
                $experience,
                $postsUsed
            );

            // Kontrollerer, om opslaggrænsen er nået.
            if (
                $postCounts['postsRemaining'] !== null &&
                $postCounts['postsRemaining'] <= 0
            ) {
                throw new \RuntimeException(
                    'Der er ikke flere opslag tilbage i denne oplevelse.'
                );
            }

            // Opretter først selve opslaget.
            $postId = $this->create(
                $experienceId,
                $caption,
                $location
            );

            // Gemmer derefter billederne på opslaget.
            foreach ($images as $index => $image) {
                $this->addImage(
                    $postId,
                    $image['public_id'],
                    (int) $image['version'],
                    $image['format'],
                    $image['alt_text'] ?? null,
                    $index + 1
                );
            }

            // Returnerer ID'et på det nye opslag.
            return $postId;
        });
    }


}
