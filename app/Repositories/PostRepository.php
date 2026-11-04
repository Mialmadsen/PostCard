<?php
// Repository til oprettelse, redigering og sletning af opslag.

declare(strict_types=1);

namespace PostCard\Repositories;

// Klassen håndterer opslag i databasen.
// extends Repository betyder, at klassen arver de fælles
// databasefunktioner fra vores parent-klasse Repository.php.
class PostRepository extends Repository
{
    // Repository til at håndtere billeder på opslag.
    private PostImageRepository $postImageRepository;
    
    public function __construct()
    {
        // Opretter den fælles databaseforbindelse fra Repository.php.
        parent::__construct();
        // Opretter repository til opslagets billeder.
        $this->postImageRepository = new PostImageRepository();
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
                $this->postImageRepository->addImage(
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


    // Henter et bestemt opslag, hvis brugeren er aktivt medlem af gruppen.
    public function findById(int $postId, int $userId): ?array
    {
        $sql = "
            SELECT
                p.post_id AS id,
                p.experience_id,
                p.caption,
                p.location,
                p.created_at AS date,
                e.name AS experience_name,
                e.group_id,
                g.name AS group_name,
                u.user_id AS author_id,
                u.first_name,
                u.last_name,

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
            INNER JOIN experiences AS e
                ON p.experience_id = e.experience_id
            INNER JOIN user_groups AS g
                ON e.group_id = g.group_id
            INNER JOIN users AS u
                ON e.user_id = u.user_id
            INNER JOIN group_members AS gm
                ON gm.group_id = e.group_id
                AND gm.user_id = :user_id
                AND gm.member_status = 'active'

            WHERE p.post_id = :post_id
                AND e.experience_status IN ('approved', 'closed')
            LIMIT 1
        ";

        return $this->fetchOne($sql, [
            'post_id' => $postId,
            'user_id' => $userId
        ]);
    }


    // Opdaterer tekst og lokation på brugerens eget opslag.
    public function update(int $postId, int $userId, string $caption, string $location): int
    {
        $sql = "
            UPDATE posts
            SET caption = :caption,
                location = :location
            WHERE post_id = :post_id
                AND experience_id IN (
                    SELECT experience_id
                    FROM experiences
                    WHERE user_id = :user_id
                )
        ";

        return $this->execute($sql, [
            'caption' => $caption,
            'location' => $location,
            'post_id' => $postId,
            'user_id' => $userId
        ]);
    }


    // Sletter et opslag, hvis det tilhører den indloggede bruger.
    public function delete(int $postId, int $userId): int
    {
        $sql = "
            DELETE FROM posts
            WHERE post_id = :post_id
                AND experience_id IN (
                    SELECT experience_id
                    FROM experiences
                    WHERE user_id = :user_id
                )
        ";

        // CASCADE sletter automatisk tilknyttede rækker
        // i comments, likes, post_views og post_images.
        return $this->execute($sql, [
            'post_id' => $postId,
            'user_id' => $userId
        ]);
    }


    // Gemmer alle ændringer til opslag og billeder i én transaktion.
    public function saveImageChanges(
        int $postId,
        int $userId,
        string $caption,
        string $location,
        array $keptImages,
        array $removeIds,
        array $savedAltTexts,
        array $uploadedImages,
        string $cover
    ): void {
        $this->transaction(function () use (
            $postId, $userId, $caption, $location, $keptImages,
            $removeIds, $savedAltTexts, $uploadedImages, $cover
        ): void {
            // Låser opslaget og kontrollerer ejerskab.
            $post = $this->fetchOne("
                SELECT p.post_id
                FROM posts AS p
                INNER JOIN experiences AS e ON p.experience_id = e.experience_id
                WHERE p.post_id = :post_id AND e.user_id = :user_id
                FOR UPDATE
            ", ['post_id' => $postId, 'user_id' => $userId]);
            if ($post === null) {
                throw new \RuntimeException('Du har ikke adgang til at ændre opslaget.');
            }
            // Kontrollerer billederne igen, mens opslaget er låst.
            $currentImages = $this->postImageRepository->findImagesByPost($postId);
            $currentIds = array_map(fn($image) => (int) $image['image_id'], $currentImages);
            $expectedIds = array_merge(
                array_map(fn($image) => (int) $image['image_id'], $keptImages),
                array_map('intval', array_keys($removeIds))
            );
            sort($currentIds);
            sort($expectedIds);
            if ($currentIds !== $expectedIds) {
                throw new \RuntimeException('Billederne er blevet ændret. Genindlæs siden og prøv igen.');
            }
            // Opdaterer opslagets tekst og lokation.
            $this->update($postId, $userId, $caption, $location);
            // Sletter billeder, som brugeren har fjernet.
            foreach (array_keys($removeIds) as $imageId) {
                $this->postImageRepository->deleteImage((int) $imageId, $postId, $userId);
            }
            // Opdaterer alt-tekster på de billeder, der beholdes.
            foreach ($keptImages as $image) {
                $imageId = (int) $image['image_id'];
                $altText = $savedAltTexts[$imageId] ?? $image['alt_text'] ?? '';
                $this->postImageRepository->updateImageAltText($imageId, $postId, $userId, $altText);
            }
            // Gemmer nye billeder i databasen.
            $newImageIds = [];
            foreach ($uploadedImages as $index => $image) {
                $newImageIds[$index] = $this->postImageRepository->addImage(
                    $postId,
                    $image['public_id'],
                    (int) $image['version'],
                    $image['format'],
                    $image['alt_text'],
                    count($keptImages) + $index + 1
                );
            }
            // Finder ID'et på det valgte forsidebillede.
            if (str_starts_with($cover, 'saved-')) {
                $coverId = (int) substr($cover, 6);
            } else {
                $coverIndex = (int) substr($cover, 4);
                $coverId = $newImageIds[$coverIndex];
            }
            // Forsidebilledet placeres først, derefter de øvrige billeder.
            $allIds = array_merge(
                array_map(fn($image) => (int) $image['image_id'], $keptImages),
                array_values($newImageIds)
            );
            $allIds = array_values(array_filter($allIds, fn($id) => $id !== $coverId));
            array_unshift($allIds, $coverId);
            foreach ($allIds as $index => $imageId) {
                $this->postImageRepository->updateImageSortOrder($imageId, $postId, $userId, $index + 1);
            }
        });
    }

}
