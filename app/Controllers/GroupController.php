<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\Auth;
use PostCard\Core\View;
use PostCard\Repositories\GroupRepository;
use PostCard\Repositories\InvitationRepository;
use PostCard\Core\Config;
use PostCard\Core\Session;

// Create a group page , the log in is checked, the form is rendered, read and validated, stores errors.
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
        //the group id from the address
        $groupId = filter_input(INPUT_GET, 'group_id', FILTER_VALIDATE_INT);

        //if id is not valid we stop and give 404

        if (!is_int($groupId) || $groupId < 1) {
            http_response_code(404);
            echo 'Gruppen findes ikke.';
            exit;
        }

        //only the group admin can invite so we check that it is the right admin
        Auth::requireGroupAdmin($groupId);

        //now we get the group communicating with DB
        $groups = new GroupRepository();
        $group = $groups->findById($groupId);
        $inviteLink = Session::takeFlash('invite_link');

        //Now we show the page where we create invitation
        View::render(
            'invite',
            [
                'pageTitle' => 'Inviter · ' . $group['name'],
                'isLoggedIn' => true,
                'group' => $group,
                'inviteLink' => $inviteLink,
                'pageStyle' => 'invite',
                'pageScripts' => ['copy-link', 'confirm'],

            ]
        );
    }

    public function createInvitation(): void
    {
        // we read group id from the form and check it id it is whole number
        $groupId = filter_var(input('group_id'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);

        if ($groupId === false) {
            http_response_code(404);
            echo 'Gruppen findes ikke.';
            exit;
        }

        Auth::requireGroupAdmin($groupId);

        //Make the token
        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);

        $invitations = new InvitationRepository();
        $invitations->create($groupId, Auth::userId(), $tokenHash);

        // build the full link
        $link = Config::get('APP_URL') . '/join?token=' . $token;
        Session::flash('invite_link', $link);

        redirect('/invite?group_id=' . $groupId);
    }
}
