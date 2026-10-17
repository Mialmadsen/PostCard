<?php
// Filen står for at hente og oprette oplevelser i databasen.
// Vi bruger en SQL-query med JOIN, så vi kan hente både oplevelsens oplysninger,
// gruppens navn og brugeren, der har oprettet den.

// Sørger for, at PHP er mere streng med datatyper, så vi undgår fejl
declare(strict_types=1);

namespace PostCard\Repositories;

// Repository-klassen har ansvaret for at hente og oprette oplevelser i databasen.
// extends Repository betyder, at vi arver de fælles databasefunktioner
// fra parent-klassen Repository.php.
class ExperienceRepository extends Repository
{
    // Funktion, der finder én bestemt oplevelse ud fra dens ID
    // ?array betyder, at funktionen returnerer et array eller null,
    // hvis oplevelsen ikke findes.
    public function find(int $experienceId): ?array
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

        // fetchOne() kommer fra vores parent-klasse Repository.
        // Den forbereder SQL-queryen, indsætter experienceId i placeholderen,
        // udfører queryen og returnerer én række eller null.
        return $this->fetchOne($sql, [
            'experience_id' => $experienceId
        ]);
    }

    // Opretter en ny oplevelse i databasen
    // Modtager oplysningerne fra ExperienceController
    // og returnerer ID'et på den nye oplevelse.
    public function create(
        int $userId,
        int $groupId,
        string $name,
        string $description,
        string $startDate,
        string $endDate
    ): int {
        // Henter gruppens indstillinger fra databasen
        // posting_mode bestemmer, om oplevelsen skal godkendes
        // default_post_limit bestemmer gruppens standardgrænse for opslag
        $group = $this->fetchOne(
            "SELECT posting_mode, default_post_limit
             FROM user_groups
             WHERE group_id = :group_id",
            ['group_id' => $groupId]
        );

        // Kontrollerer, at gruppen findes, Hvis ikke kommer en fejl
        if ($group === null) {
            throw new \InvalidArgumentException('Gruppen findes ikke.');
        }

        // Bestemmer oplevelsens status ud fra gruppens posting_mode
        // open betyder, at oplevelsen bliver godkendt med det samme
        // permission betyder, at den skal godkendes af en gruppeadministrator
        $status = $group['posting_mode'] === 'open'
            ? 'approved'
            : 'pending';

        // Henter gruppens standardgrænse for opslag
        // Hvis gruppen ikke har en grænse, gemmer vi 20 som standardværdi
        $postLimit = $group['default_post_limit'] !== null
        ? (int) $group['default_post_limit']
        : 20;

        // SQL-query til at indsætte den nye oplevelse i experiences-tabellen
        // Vi bruger placeholders i stedet for at indsætte værdier direkte
        $sql = "
            INSERT INTO experiences (
                user_id,
                group_id,
                name,
                description,
                start_date,
                end_date,
                experience_status,
                post_limit
            )
            VALUES (
                :user_id,
                :group_id,
                :name,
                :description,
                :start_date,
                :end_date,
                :experience_status,
                :post_limit
            )
        ";

        // Forbereder SQL-queryen gennem den fælles databaseforbindelse
        // Prepared statements beskytter mod SQL injection
        $stmt = $this->pdo->prepare($sql);

        // Indsætter værdierne i placeholders og udfører queryen
        $stmt->execute([
            'user_id' => $userId,
            'group_id' => $groupId,
            'name' => $name,
            'description' => $description,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'experience_status' => $status,
            'post_limit' => $postLimit
        ]);

        // Returnerer ID'et på den oplevelse, vi lige har oprettet
        // Det kan vi senere bruge til at sende brugeren til oplevelsens side
        return (int) $this->pdo->lastInsertId();
    }
}
