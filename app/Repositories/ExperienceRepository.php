<?php
// filen står for at hente oplevelser fra databasen. 
// Det bruger vores eksisterende databaseforbindelse og en SQL-query med JOIN, så vi 
// kan hente både oplevelsens oplysninger, gruppens navn og brugeren, der har oprettet den.


// Sørger for, at PHP er mere streng med datatyper så vi undgår fejl
declare(strict_types=1);

namespace PostCard\Repositories;

// bruger projektets databaseklasse og PDO
use PostCard\Core\Database;
use PDO;

// Repository-klassen har ansvaret for at hente oplevelser fra databasen
class ExperienceRepository
{
    private PDO $pdo;

    public function __construct()
    {
        // Henter den database connection, som allerede er lavet i Database.php
        $this->pdo = Database::connection();
    }

    // Funktion, der finder én bestemt oplevelse ud fra dens ID
    public function find(int $experienceId): ?array                 // ?array: funktionen returnerer et array eller null, hvis oplevelsen ikke findes
    {
        // SQL-query
        $sql = "
            SELECT
                e.experience_id,
                e.name,
                e.description,
                e.start_date,
                e.end_date,
                e.group_id,
                e.user_id,
                e.experience_status,
                e.post_limit,
                g.name AS group_name,
                g.posting_mode,
                u.first_name,
                u.last_name

            -- Vi henter oplevelsen fra experiences-tabellen
            -- AS e er et kortere navn, vi bruger i vores SQL
            FROM experiences AS e

            -- JOIN forbinder oplevelsen med den gruppe, den tilhører
            -- De to tabeller forbindes gennem group_id
            INNER JOIN user_groups AS g
                ON e.group_id = g.group_id

            -- JOIN forbinder oplevelsen med brugeren, der har oprettet den
            -- De to tabeller forbindes gennem user_id
            INNER JOIN users AS u
                ON e.user_id = u.user_id

            -- Vi henter kun den oplevelse, der har det valgte ID
            -- :experience_id er en placeholder, som får sin værdi senere
            WHERE e.experience_id = :experience_id

            LIMIT 1
        ";

        // Forbereder SQL-queryen, inden den bliver kørt
        $stmt = $this->pdo->prepare($sql);

        // Kører queryen og indsætter oplevelsens ID i placeholderen
        $stmt->execute([
            'experience_id' => $experienceId
        ]);

        // Henter resultatet fra databasen
        // fetch() henter én række, som vi gemmer i variablen
        $experience = $stmt->fetch();

        // Returnerer oplevelsen, hvis den findes
        // Hvis den ikke findes, returnerer vi null
        return $experience ?: null;
    }
}
