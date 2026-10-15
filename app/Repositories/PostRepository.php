<?php
// filen henter alle opslag fra en bestemt oplevelse. 
// experience-ID'et bruges til at finde de rigtige opslag
// subqueries til at hente det første billede og tælle likes, kommentarer og visninger.


// Sørger for, at PHP er mere streng med datatyper så vi undgår fejl
declare(strict_types=1);


namespace PostCard\Repositories;

// brug af vores databaseklasse og PDO fra database.php
use PostCard\Core\Database;
use PDO;

// klasse der kan hente opslag fra databasen
class PostRepository
{
    // private betyder, at den kun kan bruges inde i klassen
    private PDO $pdo;

    public function __construct()
    {
        // Henter vores eksisterende databaseforbindelse
        $this->pdo = Database::connection();
    }

    // Henter alle opslag, der hører til en bestemt oplevelse
    // Funktionen modtager oplevelsens ID og returnerer et array med opslag
    public function findByExperience(int $experienceId): array
    {
        // SQL-query, der henter opslag og deres tilhørende oplysninger
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

                -- Tæller hvor mange registrerede visninger opslaget har
                (
                    SELECT COUNT(*)
                    FROM post_views AS pv
                    WHERE pv.post_id = p.post_id
                ) AS views

            -- Henter opslagene fra posts-tabellen
            -- AS p er et kortere navn for tabellen
            FROM posts AS p

            -- Henter kun opslag fra den valgte oplevelse
            WHERE p.experience_id = :experience_id

            -- post_id bruges også til sortering, hvis datoerne er ens
            ORDER BY p.created_at DESC, p.post_id DESC
        ";

        $stmt = $this->pdo->prepare($sql);

        // Kører queryen og indsætter oplevelsens ID
        $stmt->execute([
            'experience_id' => $experienceId
        ]);

        // Resultatet returneres som et array
        return $stmt->fetchAll();       // fetchAll() henter alle opslag, der passer til vores query

    }
}
