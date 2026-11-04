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
        // The group id from the address
        $groupId = queryId('group_id');

        // If the id is not valid, we stop and give 404
        if ($groupId === null) {
            abort(404, 'Gruppen findes ikke.');
        }

        // Guard: ANY member may see the list
        Auth::requireMember($groupId);

        // Now we get the group from the database
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

    // It reads the two ids from the form, checks that the user is admin of that group, makes the change, saves a message, and sends the user back to the members page
    public function makeAdmin(): void
    {
        $groupId = inputId('group_id');
        $userId = inputId('user_id');

        if ($groupId === null || $userId === null) {
            abort(404, 'Medlemmet findes ikke.');
        }

        Auth::requireGroupAdmin($groupId);
        $members = new GroupMemberRepository();
        $isMadeAdmin = $members->makeAdmin($groupId, $userId);
        Session::flash('member_message', $isMadeAdmin ? 'Medlemmet er nu admin.' : 'Medlemmet kunne ikke gøres til admin. Personen er måske allerede admin eller ikke længere medlem.');

        redirect('/members?group_id=' . $groupId);
    }

    // Removes the user
    public function removeMember(): void
    {
        $groupId = inputId('group_id');
        $userId = inputId('user_id');

        if ($groupId === null || $userId === null) {
            abort(404, 'Medlemmet findes ikke.');
        }

        Auth::requireGroupAdmin($groupId);
        $members = new GroupMemberRepository();
        $isRemoved = $members->removeMember($groupId, $userId);
        Session::flash('member_message', $isRemoved ? 'Medlemmet er fjernet fra gruppen.' : 'Medlemmet kunne ikke fjernes. Personen er måske admin eller ikke længere medlem.');

        redirect('/members?group_id=' . $groupId);
    }

    // To leave the group yourself
    public function leaveGroup(): void
    {
        $groupId = inputId('group_id');

        if ($groupId === null) {
            abort(404, 'Gruppen findes ikke.');
        }

        Auth::requireMember($groupId);
        $userId = Auth::userId();
        $members = new GroupMemberRepository();
        try {
            $members->leave($groupId, $userId);
        } catch (\PDOException $exception) {
            // Only code 45000. Any other database error goes through
            if ($exception->getCode() !== '45000') {
                throw $exception;
            }

            Session::flash('member_message', 'Du er den eneste admin. Gør et andet medlem til admin, før du forlader gruppen.');
            redirect('/members?group_id=' . $groupId);
        }

        redirect('/home');
    }
}