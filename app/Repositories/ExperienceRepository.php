<?php
// Filen står for at hente og oprette oplevelser i databasen.
// Vi bruger SQL-queries med JOIN, så vi kan hente både oplevelsens oplysninger,
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

        // Kontrollerer, at gruppen findes
        if ($group === null) {
            throw new \InvalidArgumentException('Gruppen findes ikke.');
        }

        
        // Henter brugerens rolle i gruppen
        $member = $this->fetchOne(
            "SELECT member_role FROM group_members
             WHERE user_id = :user_id
                AND group_id = :group_id
                AND member_status = 'active'",
            [
                'user_id' => $userId,
                'group_id' => $groupId
            ]
        );

        // Åbne grupper og gruppeadministratorer kræver ikke godkendelse
        $status = ($group['posting_mode'] === 'open' || ($member['member_role'] ?? null) === 'group_admin')
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

        
        // Indsætter oplevelsen gennem den fælles Repository-klasse
        // insert() returnerer ID'et på den nye oplevelse
        return $this->insert($sql, [
            'user_id' => $userId,
            'group_id' => $groupId,
            'name' => $name,
            'description' => $description,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'experience_status' => $status,
            'post_limit' => $postLimit
        ]);

    }

    // Henter alle oplevelser, som en bestemt bruger har oprettet
    public function findByUser(int $userId): array
    {
        // Henter oplevelserne sammen med gruppens navn
        $sql = "
            SELECT
                e.experience_id,
                e.name,
                e.start_date,
                e.end_date,
                e.experience_status,
                e.post_limit,
                e.group_id,
                g.name AS group_name,
                g.posting_mode,
                (SELECT COUNT(*)
                 FROM posts AS p
                 WHERE p.experience_id = e.experience_id) AS post_count
            FROM experiences AS e
            INNER JOIN user_groups AS g
                ON e.group_id = g.group_id
            INNER JOIN group_members AS gm
                ON gm.group_id = e.group_id
                AND gm.user_id = :user_id
                AND gm.member_status = 'active'
            WHERE e.user_id = :owner_id
            ORDER BY e.start_date DESC
        ";

        // Returnerer alle oplevelser, der matcher brugerens ID
        return $this->fetchAll($sql, [
            'user_id' => $userId,
            'owner_id' => $userId
        ]);
    }

    // Henter godkendte og afsluttede oplevelser i en bestemt gruppe
    public function findVisibleByGroup(int $groupId): array
    {
        $sql = "
            SELECT
                e.experience_id,
                e.name,
                e.description,
                e.start_date,
                e.end_date,
                e.experience_status,
                e.post_limit,
                u.first_name,
                u.last_name,
                (SELECT COUNT(*)
                 FROM posts AS p
                 WHERE p.experience_id = e.experience_id) AS post_count
            FROM experiences AS e
            INNER JOIN users AS u
                ON e.user_id = u.user_id
            WHERE e.group_id = :group_id
                AND e.experience_status IN ('approved', 'closed')
            ORDER BY e.start_date DESC
        ";

        return $this->fetchAll($sql, [
            'group_id' => $groupId
        ]);
    }

    // Henter alle oplevelser, der afventer godkendelse i en bestemt gruppe
    public function findPendingByGroup(int $groupId): array
    {
        $sql = "
            SELECT
                e.experience_id,
                e.name,
                e.description,
                e.start_date,
                e.end_date,
                e.post_limit,
                e.created_at,
                u.first_name,
                u.last_name
            FROM experiences AS e
            INNER JOIN users AS u
                ON e.user_id = u.user_id
            WHERE e.group_id = :group_id
                AND e.experience_status = 'pending'
            ORDER BY e.created_at ASC, e.experience_id ASC
        ";

        // Returnerer de anmodninger, der endnu ikke er behandlet
        return $this->fetchAll($sql, [
            'group_id' => $groupId
        ]);
    }

    // Godkender eller afviser en afventende oplevelse
    // Administratoren kan samtidig ændre datoer og opslagsgrænse
    public function updateRequest(
        int $experienceId,
        int $groupId,
        string $status,
        ?string $startDate = null,
        ?string $endDate = null,
        ?int $postLimit = null
    ): bool {
        // Kontrollerer, at status er gyldig
        if (!in_array($status, ['approved', 'rejected'], true)) {
            throw new \InvalidArgumentException('Ugyldig status.');
        }

        // Opdaterer kun en oplevelse, der stadig afventer godkendelse
        // COALESCE beholder den eksisterende værdi, hvis vi sender null
        $sql = "
            UPDATE experiences
            SET
                experience_status = :status,
                start_date = COALESCE(:start_date, start_date),
                end_date = COALESCE(:end_date, end_date),
                post_limit = COALESCE(:post_limit, post_limit)
            WHERE experience_id = :experience_id
                AND group_id = :group_id
                AND experience_status = 'pending'
        ";

       
        // Opdaterer anmodningen gennem den fælles Repository-klasse
        // execute() returnerer antallet af ændrede rækker
        return $this->execute($sql, [
            'experience_id' => $experienceId,
            'group_id' => $groupId,
            'status' => $status,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'post_limit' => $postLimit
        ]) === 1;

    }
}
