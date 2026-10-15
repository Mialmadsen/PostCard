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
}
