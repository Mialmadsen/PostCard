<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\Auth;
use PostCard\Core\View;
use PostCard\Repositories\GroupRepository;
use PostCard\Repositories\InvitationRepository;
use PostCard\Repositories\GroupMemberRepository;
use PostCard\Core\Config;
use PostCard\Core\Session;

// Create a group page: the login is checked, the form is rendered, read and validated, and errors are stored.
class GroupController
{
    public function create(): void
    {
        Auth::requireLogin();
        View::render('create-group', [
            'pageTitle' => 'Ny gruppe',
            'isLoggedIn' => true,
            'errors' => [],
            'old' => [],
        ]);
    }

    public function store(): void
    {
        Auth::requireLogin();
        $name = input('name');
        $description = input('description');
        $postingMode = input('posting_mode');
        $postLimitText = input('default_post_limit');

        $errors = [];

        if ($name === '') {
            $errors['name'] = 'Skriv gruppens navn.';
        } elseif (mb_strlen($name) > 100) {
            $errors['name'] = 'Navnet må højst være 100 tegn.';
        }

        if (mb_strlen($description) > 1000) {
            $errors['description'] = 'Beskrivelsen må højst være 1000 tegn.';
        }

        if (!in_array($postingMode, ['open', 'permission'], true)) {
            $errors['posting_mode'] = 'Vælg, hvem der må dele oplevelser.';
        }

        $defaultPostLimit = filter_var($postLimitText, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 50],
        ]);

        if ($defaultPostLimit === false) {
            $errors['default_post_limit'] = 'Skriv et helt tal mellem 1 og 50';
        }

        // We check if there are any errors and either stop and send the person back to form, or continue and create a new group
        if ($errors !== []) {
            http_response_code(422);
            View::render('create-group', [
                'pageTitle' => 'Ny gruppe',
                'isLoggedIn' => true,
                'errors' => $errors,
                'old' => [
                    'name' => $name,
                    'description' => $description,
                    'posting_mode' => $postingMode,
                    'default_post_limit' => $postLimitText,
                ],
            ]);
            return;
        }

        // The happy end of creating a new group, save and go on
        $descriptionOrNull = $description === '' ? null : $description;
        $groups = new GroupRepository();
        $groupId = $groups->createWithAdmin(
            Auth::userId(),
            $name,
            $descriptionOrNull,
            $postingMode,
            $defaultPostLimit,
        );

        redirect('/group?id=' . $groupId);
    }

    public function invite(): void
    {
        // The group id from the address
        $groupId = queryId('group_id');

        // If the id is not valid, we stop and give 404
        if ($groupId === null) {
            abort(404, 'Gruppen findes ikke.');
        }

        // Only the group admin can invite, so we check that it is the right admin
        Auth::requireGroupAdmin($groupId);

        // Now we get the group from the database
        $groups = new GroupRepository();
        $group = $groups->findById($groupId);
        $inviteLink = Session::takeFlash('invite_link');
        $inviteMessage = Session::takeFlash('invite_message');

        // The group's links for "Dine links", with status waiting / used / expired
        $invitations = new InvitationRepository();
        $groupInvitations = $invitations->findByGroup($groupId);

        // Now we show the page where we create invitations
        View::render(
            'invite',
            [
                'pageTitle' => 'Inviter · ' . $group['name'],
                'isLoggedIn' => true,
                'group' => $group,
                'inviteLink' => $inviteLink,
                'invitations' => $groupInvitations,
                'inviteMessage' => $inviteMessage,
                'pageStyle' => 'invite',
                'pageScripts' => ['copy-link', 'confirm'],
            ]
        );
    }

    public function createInvitation(): void
    {
        // We read the group id from the form and check if it is a whole number
        $groupId = inputId('group_id');

        if ($groupId === null) {
            abort(404, 'Gruppen findes ikke.');
        }

        Auth::requireGroupAdmin($groupId);

        // Make the token
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);

        $invitations = new InvitationRepository();
        $invitations->create($groupId, Auth::userId(), $tokenHash);

        // Build the full link
        $link = Config::get('APP_URL') . '/join?token=' . $token;
        Session::flash('invite_link', $link);

        redirect('/invite?group_id=' . $groupId);
    }

    public function cancelInvitation(): void
    {
        // We read the two ids from the form and check if they are whole numbers
        $groupId = inputId('group_id');
        $invitationId = inputId('invitation_id');

        if ($groupId === null || $invitationId === null) {
            abort(404, 'Linket findes ikke.');
        }

        Auth::requireGroupAdmin($groupId);

        $invitations = new InvitationRepository();

        $isCancelled = $invitations->cancel($invitationId, $groupId);

        Session::flash('invite_message', $isCancelled ? 'Linket er annulleret.' : 'Linket kunne ikke annulleres. Det er allerede brugt eller udløbet.');

        redirect('/invite?group_id=' . $groupId);
    }

    public function join(): void
    {
        $token = filter_input(INPUT_GET, 'token');

        // 1. Look up the link, but only if the token is a usable string
        $invitation = null;
        if (is_string($token) && $token !== '' && strlen($token) <= 100) {
            $invitations = new InvitationRepository();
            $invitation = $invitations->findValidByTokenHash(hash('sha256', $token));
        }

        // 2. Unusable link: only the "Linket virker ikke" state, nothing about the group
        if ($invitation === null) {
            http_response_code(404);
            View::render('join', [
                'pageTitle' => 'Invitation',
                'isLoggedIn' => Auth::userId() !== null,
                'state' => 'invalid',
            ]);
            return;
        }

        // 3. Which of the other three states?
        $userId = Auth::userId();
        $members = new GroupMemberRepository();

        if ($userId === null) {
            $state = 'guest';
        } elseif ($members->findActiveRole($userId, $invitation['group_id']) !== null) {
            $state = 'member';
        } else {
            $state = 'join';
        }

        View::render('join', [
            'pageTitle' => 'Invitation',
            'isLoggedIn' => $userId !== null,
            'state' => $state,
            'invitation' => $invitation,
            'memberCount' => $members->countActiveMembers($invitation['group_id']),
            'token' => $token,
        ]);
    }

    public function acceptInvitation(): void
    {
        Auth::requireLogin();
        $token = input('token');
        $invitations = new InvitationRepository();

        $invitation = null;
        if ($token !== '' && strlen($token) <= 100) {
            $invitation = $invitations->findValidByTokenHash(hash('sha256', $token));
        }

        if ($invitation === null) {
            redirect('/join?token=' . urlencode($token));
        }

        $userId = Auth::userId();
        $members = new GroupMemberRepository();

        if ($members->findActiveRole($userId, $invitation['group_id']) !== null) {
            redirect('/group?id=' . $invitation['group_id']);
        }

        $joined = $invitations->accept($invitation['invitation_id'], $invitation['group_id'], $userId);

        if (!$joined) {
            redirect('/join?token=' . urlencode($token));
        }

        redirect('/group?id=' . $invitation['group_id']);
    }
}
