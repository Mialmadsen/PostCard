<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\Auth;
use PostCard\Core\View;
use PostCard\Repositories\GroupRepository;

use PostCard\Repositories\GroupMemberRepository;

class MemberController
{

    public function index(): void
    {

        //the group id from the address
        $groupId = filter_input(INPUT_GET, 'group_id', FILTER_VALIDATE_INT);

        //if id is not valid we stop and give 404

        if (!is_int($groupId) || $groupId < 1) {
            http_response_code(404);
            echo 'Gruppen findes ikke.';
            exit;
        }

        //guard: ANY member may see the list 
        Auth::requireMember($groupId);

        //now we get the group communicating with DB
        $groups = new GroupRepository();
        $group = $groups->findById($groupId);
        $members = new GroupMemberRepository();
        $memberList = $members->findActiveByGroup($groupId);
        $role = $members->findActiveRole(Auth::userId(), $groupId);

        View::render(
            'members',
            [
                'pageTitle' => 'Medlemmer · ' . $group['name'],
                'isLoggedIn' => true,
                'group' => $group,
                'members' => $memberList,
                'currentUserId' => Auth::userId(),
                'isAdmin' => $role === 'group_admin',
                'pageStyle' => 'members',
                'pageScripts' => ['confirm'],
            ]
        );
    }
}
