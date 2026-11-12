<?php

declare(strict_types=1);

namespace PostCard\Repositories;

class AdminPostRepository extends Repository
{
    // Henter opslag fra hele PostCard.
    // Kan filtrere efter søgetekst og gruppe.
    public function findAll(string $search = '', ?int $groupId = null): array
    {
        $sql = "
            SELECT
                p.post_id AS id,
                p.location,
                p.caption,
                p.created_at,
                e.user_id AS author_id,
                e.name AS experience_name,
                g.name AS group_name,
                u.first_name,
                u.last_name,

                -- Tæller billeder til hvert opslag
                (
                    SELECT COUNT(*)
                    FROM post_images AS pi
                    WHERE pi.post_id = p.post_id
                ) AS image_count

            FROM posts AS p

            JOIN experiences AS e
                ON p.experience_id = e.experience_id

            JOIN user_groups AS g
                ON e.group_id = g.group_id

            JOIN users AS u
                ON e.user_id = u.user_id

            WHERE 1 = 1
        ";

        $params = [];

        // Hvis operatoren har skrevet noget i søgefeltet.
        if ($search !== '') {
            $sql .= "
                AND (
                    p.location LIKE :search_location
                    OR p.caption LIKE :search_caption
                    OR e.name LIKE :search_experience
                )
            ";

            $searchTerm = '%' . $search . '%';

            $params['search_location'] = $searchTerm;
            $params['search_caption'] = $searchTerm;
            $params['search_experience'] = $searchTerm;
        }

        // Hvis operatoren har valgt en bestemt gruppe.
        if ($groupId !== null) {
            $sql .= " AND g.group_id = :group_id";

            $params['group_id'] = $groupId;
        }

        // Nyeste opslag vises først.
        $sql .= "
            ORDER BY p.created_at DESC, p.post_id DESC
        ";

        return $this->fetchAll($sql, $params);
    }

    // Henter alle grupper til dropdown-menuen.
    public function findAllGroups(): array
    {
        $sql = "
            SELECT
                group_id,
                name
            FROM user_groups
            ORDER BY name ASC
        ";

        return $this->fetchAll($sql);
    }


    
    // Henter et opslag til operatoren på tværs af alle grupper.
    public function findById(int $postId): ?array
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
                (SELECT COUNT(*) FROM likes AS l WHERE l.post_id = p.post_id) AS likes,
                (SELECT COUNT(*) FROM comments AS c WHERE c.post_id = p.post_id) AS comments,
                (SELECT COUNT(*) FROM post_views AS pv WHERE pv.post_id = p.post_id) AS views
            FROM posts AS p
            JOIN experiences AS e ON p.experience_id = e.experience_id
            JOIN user_groups AS g ON e.group_id = g.group_id
            JOIN users AS u ON e.user_id = u.user_id
            WHERE p.post_id = :post_id
            LIMIT 1
        ";
        return $this->fetchOne($sql, ['post_id' => $postId]);
    }



    // Sletter et opslag fra PostCard uanset gruppe og forfatter.
    // Metoden må kun kaldes fra en operator-beskyttet funktion.
    public function delete(int $postId): int
    {
        $sql = "
            DELETE FROM posts
            WHERE post_id = :post_id
        ";

        return $this->execute($sql, [
            'post_id' => $postId
        ]);
    }


}
