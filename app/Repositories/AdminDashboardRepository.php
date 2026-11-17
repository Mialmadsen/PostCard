<?php

declare(strict_types=1);

namespace PostCard\Repositories;

// The numbers on the operator's dashboard: PostCard as a whole.
class AdminDashboardRepository extends Repository
{
    // Four counts in one trip to the database
    public function findPlatformCounts(): array
    {
        return $this->fetchOne(
            "SELECT
                (SELECT COUNT(*) FROM users WHERE user_status <> 'deleted') AS user_count,
                (SELECT COUNT(*) FROM user_groups) AS group_count,
                (SELECT COUNT(*) FROM posts) AS post_count,
                (SELECT COUNT(*) FROM users WHERE user_status = 'blocked') AS blocked_count"
        );
    }
}
