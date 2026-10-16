<?php
// Filen henter alle opslag fra en bestemt oplevelse.
// Experience-ID'et bruges til at finde de rigtige opslag.
// Vi bruger subqueries til at hente det første billede
// og tælle likes, kommentarer og visninger.

// Sørger for, at PHP er mere streng med datatyper, så vi undgår fejl
declare(strict_types=1);

namespace PostCard\Repositories;

// Klassen henter opslag fra databasen.
// extends Repository betyder, at klassen arver de fælles
// databasefunktioner fra vores parent-klasse Repository.php.
class PostRepository extends Repository
{
    // Henter alle opslag, der hører til en bestemt oplevelse.
    // Funktionen modtager oplevelsens ID og returnerer et array med opslag.
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

        // fetchAll() kommer fra vores parent-klasse Repository.
        // Den forbereder SQL-queryen og indsætter experienceId i placeholderen.
        // Derefter udfører den queryen og returnerer alle opslag som et array.
        // Hvis der ikke findes nogen opslag, returnerer den et tomt array.
        return $this->fetchAll($sql, [
            'experience_id' => $experienceId
        ]);
    }
}
