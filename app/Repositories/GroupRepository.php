<?php

declare(strict_types=1);

namespace PostCard\Repositories;

// All SQL for the group table.
class GroupRepository extends Repository
{
    public function createWithAdmin(
        int $userId,
        string $name,
        ?string $description,
        string $postingMode,
        int $defaultPostLimit
    ): int {
        return $this->transaction(function () use ($userId, $name, $description, $postingMode, $defaultPostLimit) {
            $groupId = $this->insert(
                'INSERT INTO user_groups (name, description, posting_mode, default_post_limit)
                 VALUES (:name, :description, :posting_mode, :default_post_limit)',
                [
                    'name' => $name,
                    'description' => $description,
                    'posting_mode' => $postingMode,
                    'default_post_limit' => $defaultPostLimit,
                ]
            );

            $this->insert(
                'INSERT INTO group_members (user_id, group_id, member_role)
                 VALUES (:user_id, :group_id, :member_role)',
                [
                    'user_id' => $userId,
                    'group_id' => $groupId,
                    'member_role' => 'group_admin'
                ]
            );

            return $groupId;
        });
    }

    // Henter en bestemt gruppe ud fra dens ID
    public function findById(int $groupId): ?array
    {
        return $this->fetchOne(
            'SELECT group_id, name, description, posting_mode, default_post_limit
             FROM user_groups
             WHERE group_id = :group_id',
            ['group_id' => $groupId]
        );
    }
}