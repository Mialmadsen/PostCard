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
}
