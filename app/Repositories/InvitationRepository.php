<?php

declare(strict_types=1);

namespace PostCard\Repositories;

// All SQL for the invitations table
class InvitationRepository extends Repository
{
    // saves token's hash that is valid 7 days
    public function create(int $groupId, int $createdBy, string $tokenHash): void
    {
        $this->insert(
            'INSERT INTO invitations (group_id, created_by, token_hash, expires_at)
             VALUES (:group_id, :created_by, :token_hash, DATE_ADD(NOW(), INTERVAL 7 DAY))',
            [
                'group_id' => $groupId,
                'created_by' => $createdBy,
                'token_hash' => $tokenHash
            ]
        );
    }

    public function findByGroup(int $groupId): array
    {
        return $this->fetchAll(
            "SELECT invitation_id, created_at, expires_at, used_at,
                    CASE
                        WHEN used_at IS NOT NULL THEN 'used'
                        WHEN expires_at <= NOW() THEN 'expired'
                        ELSE 'waiting'
                    END AS status
             FROM invitations
             WHERE group_id = :group_id
             ORDER BY created_at DESC
             LIMIT 20",
            [
                'group_id' => $groupId,
            ]
        );
    }

    public function cancel(int $invitationId, int $groupId): bool
    {
        return $this->execute(
            'UPDATE invitations
             SET expires_at = NOW()
             WHERE invitation_id = :invitation_id
               AND group_id = :group_id
               AND used_at IS NULL
               AND expires_at > NOW()',
            [
                'group_id' => $groupId,
                'invitation_id' => $invitationId,
            ]
        ) === 1;
    }

    public function findValidByTokenHash(string $tokenHash): ?array
    {
        return $this->fetchOne(
            'SELECT i.invitation_id, i.group_id, g.name, g.description, u.first_name, u.last_name
             FROM invitations AS i
             JOIN user_groups AS g ON g.group_id = i.group_id
             JOIN users AS u ON u.user_id = i.created_by
             WHERE i.token_hash = :token_hash
               AND i.used_at IS NULL
               AND i.expires_at > NOW()',
            ['token_hash' => $tokenHash]
        );
    }

    //In one transaction we mark the link as used and make a person that used it to become a member
    public function accept(int $invitationId, int $groupId, int $userId): bool
    {
        return $this->transaction(function () use ($invitationId, $groupId, $userId) {
            $claimed = $this->execute(
                'UPDATE invitations
                 SET used_at = NOW()
                 WHERE invitation_id = :invitation_id
                   AND used_at IS NULL
                   AND expires_at > NOW()',
                ['invitation_id' => $invitationId]
            ) === 1;
            // Returns 1 or false
            if (!$claimed) {
                return false;
            }

            $this->execute(
                "INSERT INTO group_members (user_id, group_id)
                 VALUES (:user_id, :group_id)
                 ON DUPLICATE KEY UPDATE
                     member_status = 'active',
                     member_role = 'member',
                     joined_at = NOW()",
                [
                    'user_id' => $userId,
                    'group_id' => $groupId,
                ]
            );

            return true;
        });
    }
}
