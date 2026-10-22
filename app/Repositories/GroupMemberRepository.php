<?php

declare(strict_types=1);

namespace PostCard\Repositories;

// All SQL for the group_members table.
class GroupMemberRepository extends Repository
{
    public function findActiveRole(int $userId, int $groupId): ?string
    {
        return $this->fetchValue(
            'SELECT member_role
               FROM group_members
              WHERE user_id = :user_id
                AND group_id = :group_id
                AND member_status = \'active\'',
            ['user_id' => $userId, 'group_id' => $groupId]
        );
    }

    // Henter alle grupper, som en bestemt bruger er aktivt medlem af
    // JOIN bruges til at hente gruppens oplysninger fra user_groups
    public function findActiveGroupsByUser(int $userId): array
    {
        $sql = "
            SELECT
                g.group_id,
                g.name,
                g.posting_mode,
                gm.member_role
            FROM group_members AS gm

            INNER JOIN user_groups AS g
                ON gm.group_id = g.group_id

            WHERE gm.user_id = :user_id
                AND gm.member_status = 'active'

            ORDER BY g.name ASC
        ";

        // fetchAll() kommer fra vores fælles Repository-klasse
        // og returnerer alle grupper som et array
        return $this->fetchAll($sql, [
            'user_id' => $userId
        ]);
    }

    // Henter de grupper, hvor brugeren er aktiv gruppeadministrator
    public function findAdminGroupsByUser(int $userId): array
    {
        $sql = "
            SELECT
                g.group_id,
                g.name,
                g.posting_mode
            FROM group_members AS gm

            -- JOIN bruges til at hente gruppens oplysninger
            INNER JOIN user_groups AS g
                ON gm.group_id = g.group_id

            -- Finder kun de grupper, hvor brugeren er administrator
            WHERE gm.user_id = :user_id
                AND gm.member_status = 'active'
                AND gm.member_role = 'group_admin'

            ORDER BY g.name ASC
        ";

        // Henter grupperne gennem den fælles Repository-klasse
        return $this->fetchAll($sql, [
            'user_id' => $userId
        ]);
    }

    // Tæller aktive medlemmer i en bestemt gruppe
    public function countActiveMembers(int $groupId): int
    {
        return (int) $this->fetchValue(
            "SELECT COUNT(*)
            FROM group_members
            WHERE group_id = :group_id
            AND member_status = 'active'",
            ['group_id' => $groupId]
        );
    }

    // Henter de første 4 aktive medlemmer i en bestemt gruppe
    public function findFirstMembersByGroup(int $groupId): array
    {
        return $this->fetchAll(
            "SELECT
                u.user_id,
                u.first_name,
                u.last_name
            FROM group_members AS gm
            INNER JOIN users AS u
                ON gm.user_id = u.user_id
            WHERE gm.group_id = :group_id
                AND gm.member_status = 'active'
            ORDER BY gm.joined_at ASC, u.user_id ASC
            LIMIT 4",
            ['group_id' => $groupId]
        );
    }
}
