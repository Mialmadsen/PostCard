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
use PostCard\Repositories\AdminSettingsRepository;
use PostCard\Services\RateLimiter;

// Create a group page: the login is checked, the form is rendered, read and validated, and errors are stored.
class GroupController extends BaseController
{
    public function create(): void
    {
        Auth::requireLogin();

        // Henter standardgrænsen fra Admin Settings.
        $defaultPostLimit = $this->getDefaultPostLimit();

        View::render('create-group', [
            'pageTitle' => 'Ny gruppe',
            'isLoggedIn' => true,
            'errors' => [],
            'old' => [],
            'defaultPostLimit' => $defaultPostLimit
        ]);
    }

    public function store(): void
    {
        $userId = $this->requireUser();
        [$name, $description, $postingMode, $postLimitText] = $this->readGroupFields();

        $errors = $this->validateGroup($name, $description, $postingMode, $postLimitText);

        // Spam guard: a user may create only a few groups per day, the number is set in RateLimiter
        $limiter = new RateLimiter();
        if ($limiter->tooMany('create_group', $userId)) {
            $errors['form'] = $limiter->message('create_group');
        }

        // We check if there are any errors and either stop and send the person back to form, or continue and create a new group
        if ($errors !== []) {
            // Henter standardgrænsen, hvis formularen skal vises igen.
            $defaultPostLimit = $this->getDefaultPostLimit();
            http_response_code(isset($errors['form']) ? 429 : 422);
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
                'defaultPostLimit' => $defaultPostLimit,
            ]);
            return;
        }

        // The happy end of creating a new group, save and go on
        $descriptionOrNull = $description === '' ? null : $description;
        // validateGroup() already let only a whole number 1–50 through, so (int) just turns '20' into 20
        $defaultPostLimit = (int) $postLimitText;
        $groups = new GroupRepository();
        $groupId = $groups->createWithAdmin(
            $userId,
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
        $groupId = $this->requireQueryId('group_id', 'Gruppen findes ikke.', 404);

        // Only the group admin can invite, so we check that it is the right admin
        Auth::requireGroupAdmin($groupId);

        // Now we get the group from the database
        $groups = new GroupRepository();
        $group = $groups->findById($groupId);
        $inviteLink = Session::takeFlash('invite_link');
        $inviteMessage = Session::takeFlash('invite_message');
        $inviteError = Session::takeFlash('invite_error');

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
                'inviteError' => $inviteError,
                'pageStyle' => 'invite',
                'pageScripts' => ['copy-link', 'confirm'],
            ]
        );
    }

    public function settings(): void
    {
        // The group id from the address
        $groupId = $this->requireQueryId('group_id', 'Gruppen findes ikke.', 404);

        // Only the group admin can change it, so we check that it is the right admin
        Auth::requireGroupAdmin($groupId);

        // (your comment)
        $groups = new GroupRepository();
        $group = $groups->findById($groupId);

        $settingsMessage = Session::takeFlash('settings_message');


        View::render('group-settings', [
            'pageTitle' => 'Indstillinger · ' . $group['name'],
            'isLoggedIn' => true,
            'group' => $group,
            'settingsMessage' => $settingsMessage,
            'errors' => [],
            'old' => $group,
        ]);
    }

    public function updateSettings(): void
    {
        $groupId = $this->requireInputId('group_id', 'Gruppen findes ikke.', 404);

        Auth::requireGroupAdmin($groupId);
        [$name, $description, $postingMode, $postLimitText] = $this->readGroupFields();

        $errors = $this->validateGroup($name, $description, $postingMode, $postLimitText);
        if ($errors !== []) {
            // the saved group, for the page title, breadcrumbs and header
            $groups = new GroupRepository();
            $group = $groups->findById($groupId);

            http_response_code(422);
            View::render('group-settings', [
                'pageTitle' => 'Indstillinger · ' . $group['name'],
                'isLoggedIn' => true,
                'group' => $group,
                'errors' => $errors,
                'settingsMessage' => null,
                'old' => [
                    'name' => $name,
                    'description' => $description,
                    'posting_mode' => $postingMode,
                    'default_post_limit' => $postLimitText,
                ],
            ]);
            return;
        }

        //we save if no mistakes
        $descriptionOrNull = $description === '' ? null : $description;
        $defaultPostLimit = (int)$postLimitText;
        $groups = new GroupRepository();
        $groups->update($groupId, $name, $descriptionOrNull, $postingMode, $defaultPostLimit);

        $this->redirectWithFlash('settings_message', 'Indstillingerne er gemt.', '/group-settings?group_id=' . $groupId);
    }

    public function createInvitation(): void
    {
        // We read the group id from the form and check if it is a whole number
        $groupId = $this->requireInputId('group_id', 'Gruppen findes ikke.', 404);

        Auth::requireGroupAdmin($groupId);
        // Spam guard: an admin may make only a few links per day, the number is set in RateLimiter
        $limiter = new RateLimiter();
        if ($limiter->tooMany('create_invite', Auth::userId())) {
            $this->redirectWithFlash('invite_error', $limiter->message('create_invite'), '/invite?group_id=' . $groupId);
        }


        // Make the token
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);

        $invitations = new InvitationRepository();
        $invitations->create($groupId, Auth::userId(), $tokenHash);

        // Build the full link
        $link = Config::get('APP_URL') . '/join?token=' . $token;
        $this->redirectWithFlash('invite_link', $link, '/invite?group_id=' . $groupId);
    }

    public function cancelInvitation(): void
    {
        // We read the two ids from the form and check if they are whole numbers
        $groupId = $this->requireInputId('group_id', 'Linket findes ikke.', 404);
        $invitationId = $this->requireInputId('invitation_id', 'Linket findes ikke.', 404);

        Auth::requireGroupAdmin($groupId);

        $invitations = new InvitationRepository();
        $isCancelled = $invitations->cancel($invitationId, $groupId);

        if ($isCancelled) {
            $this->redirectWithFlash('invite_message', 'Linket er annulleret.', '/invite?group_id=' . $groupId);
        } else {
            $this->redirectWithFlash('invite_error', 'Linket kunne ikke annulleres. Det er allerede brugt eller udløbet.', '/invite?group_id=' . $groupId);
        }
    }

    public function join(): void
    {
        $token = filter_input(INPUT_GET, 'token');

        // 1. Look up the link, but only if the token is a usable string
        $invitation = $this->findValidInvitation($token);

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
        $userId = $this->requireUser();
        $token = input('token');
        $invitations = new InvitationRepository();

        $invitation = $this->findValidInvitation($token, $invitations);

        if ($invitation === null) {
            redirect('/join?token=' . urlencode($token));
        }

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

    // Gets the default post limit from Admin Settings.
    private function getDefaultPostLimit(): int
    {
        $siteSettings = (new AdminSettingsRepository())->findAll();
        return (int) ($siteSettings['default_post_limit'] ?? 20);
    }

    // Reads the same four fields for group creation and settings.
    private function readGroupFields(): array
    {
        return [
            input('name'),
            input('description'),
            input('posting_mode'),
            input('default_post_limit'),
        ];
    }

    // Looks up a valid invitation without exposing the raw token to the database.
    private function findValidInvitation(mixed $token, ?InvitationRepository $invitations = null): mixed
    {
        if (!is_string($token) || $token === '' || strlen($token) > 100) {
            return null;
        }

        $invitations ??= new InvitationRepository();
        return $invitations->findValidByTokenHash(hash('sha256', $token));
    }

    // Checks the four group fields (used by create and settings)
    private function validateGroup(string $name, string $description, string $postingMode, string $postLimitText): array
    {
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

        return $errors;
    }
}
