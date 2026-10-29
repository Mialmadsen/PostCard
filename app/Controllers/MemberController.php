<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\Auth;
use PostCard\Core\View;
use PostCard\Core\Session;
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
        $memberMessage = Session::takeFlash('member_message');


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
                'memberMessage' => $memberMessage,

            ]
        );
    }

    //it reads the two ids from the form, checks that user is admin of that group, makes the change, saves a message, and sends the user back to the members page
    public function makeAdmin(): void
    {
        $groupId = filter_var(input('group_id'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);
        $userId = filter_var(input('user_id'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);

        if ($groupId === false || $userId === false) {
            http_response_code(404);
            echo 'Medlemmet findes ikke.';
            exit;
        }

        Auth::requireGroupAdmin($groupId);
        $members = new GroupMemberRepository();
        $isMadeAdmin = $members->makeAdmin($groupId, $userId);
        Session::flash('member_message', $isMadeAdmin ? 'Medlemmet er nu admin.' : 'Medlemmet kunne ikke gøres til admin. Personen er måske allerede admin eller ikke længere medlem.');

        redirect('/members?group_id=' . $groupId);
    }

    //Removes the user
    public function removeMember(): void
    {
        $groupId = filter_var(input('group_id'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);
        $userId = filter_var(input('user_id'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);

        if ($groupId === false || $userId === false) {
            http_response_code(404);
            echo 'Medlemmet findes ikke.';
            exit;
        }

        Auth::requireGroupAdmin($groupId);
        $members = new GroupMemberRepository();
        $isRemoved = $members->removeMember($groupId, $userId);
        Session::flash('member_message', $isRemoved ? 'Medlemmet er fjernet fra gruppen.' : 'Medlemmet kunne ikke fjernes. Personen er måske admin eller ikke længere medlem.');

        redirect('/members?group_id=' . $groupId);
    }

    //To leave the group yourself
    public function leaveGroup(): void
    {
        $groupId = filter_var(input('group_id'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);


        if ($groupId === false) {
            http_response_code(404);
            echo 'Gruppen findes ikke.';
            exit;
        }

        Auth::requireMember($groupId);
        $userId = Auth::userId();
        $members = new GroupMemberRepository();
        $members->leave($groupId, $userId);


        redirect('/home');
    }
}
