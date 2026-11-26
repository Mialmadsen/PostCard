<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\Auth;
use PostCard\Core\View;
use PostCard\Core\Session;
use PostCard\Repositories\GroupRepository;
use PostCard\Repositories\GroupMemberRepository;


class MemberController extends BaseController
{
    public function index(): void
    {
        // The group id from the address
        $groupId = $this->requireQueryId('group_id', 'Gruppen findes ikke.', 404);

        // Guard: ANY member may see the list
        Auth::requireMember($groupId);

        // Now we get the group from the database
        $groups = new GroupRepository();
        $group = $groups->findById($groupId);
        $members = new GroupMemberRepository();
        $memberList = $members->findActiveByGroup($groupId);
        $role = $members->findActiveRole(Auth::userId(), $groupId);
        $memberMessage = Session::takeFlash('member_message');
        $memberError = Session::takeFlash('member_error');

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
                'memberError' => $memberError,
            ]
        );
    }

    // It reads the two ids from the form, checks that the user is admin of that group, makes the change, saves a message, and sends the user back to the members page
    public function makeAdmin(): void
    {
        $groupId = $this->requireInputId('group_id', 'Medlemmet findes ikke.', 404);
        $userId = $this->requireInputId('user_id', 'Medlemmet findes ikke.', 404);

        Auth::requireGroupAdmin($groupId);
        $members = new GroupMemberRepository();
        $isMadeAdmin = $members->makeAdmin($groupId, $userId);
        if ($isMadeAdmin) {
            Session::flash('member_message', 'Medlemmet er nu admin.');
        } else {
            Session::flash('member_error', 'Medlemmet kunne ikke gøres til admin. Personen er måske allerede admin eller ikke længere medlem.');
        }

        redirect('/members?group_id=' . $groupId);
    }

    // Removes the user
    public function removeMember(): void
    {
        $groupId = $this->requireInputId('group_id', 'Medlemmet findes ikke.', 404);
        $userId = $this->requireInputId('user_id', 'Medlemmet findes ikke.', 404);

        Auth::requireGroupAdmin($groupId);
        $members = new GroupMemberRepository();
        $isRemoved = $members->removeMember($groupId, $userId);
        if ($isRemoved) {
            Session::flash('member_message', 'Medlemmet er fjernet fra gruppen.');
        } else {
            Session::flash('member_error', 'Medlemmet kunne ikke fjernes. Personen er måske admin eller ikke længere medlem.');
        }

        redirect('/members?group_id=' . $groupId);
    }

    // To leave the group yourself
    public function leaveGroup(): void
    {
        $groupId = $this->requireInputId('group_id', 'Gruppen findes ikke.', 404);

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

            $this->redirectWithFlash(
                'member_error',
                'Du er den eneste admin. Gør et andet medlem til admin, før du forlader gruppen.',
                '/members?group_id=' . $groupId
            );
        }

        redirect('/home');
    }
}