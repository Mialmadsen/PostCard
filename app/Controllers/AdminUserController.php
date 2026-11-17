<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\Auth;
use PostCard\Core\View;
use PostCard\Repositories\UserRepository;
use PostCard\Repositories\GroupMemberRepository;
use PostCard\Core\Session;
use PostCard\Services\CloudinaryService;
use PostCard\Services\UserValidator;

// The operator's pages about users: the list, and blocking or unblocking a user.
class AdminUserController
{
    // Shows the list of all users.
    public function index(): void
    {
        // Only operators may be here. Everyone else gets 403.
        Auth::requireOperator();

        $users = new UserRepository();

        // The filters from the address, e.g. /admin/users?q=kim&status=blocked
        $search = trim((string) ($_GET['q'] ?? ''));
        $status = (string) ($_GET['status'] ?? '');

        // Only the three real statuses are allowed. Anything else means "all".
        if (!in_array($status, ['active', 'blocked', 'deleted'], true)) {
            $status = '';
        }

        $allUsers = $users->findAllForAdmin($search, $status);

        // The result of a block or unblock, shown once as a pop-up (set in updateStatus)
        $usersMessage = Session::takeFlash('admin_users_message');
        $usersError = Session::takeFlash('admin_users_error');

        View::render('admin-users', [
            'pageTitle' => 'Administration · Brugere',
            'pageStyle' => 'admin',
            'pageScripts' => ['confirm'],
            'isLoggedIn' => true,
            'isOperator' => true,
            'adminPage' => 'users',
            'users' => $allUsers,
            'search' => $search,
            'selectedStatus' => $status,
            'usersMessage' => $usersMessage,
            'usersError' => $usersError,
        ]);
    }

    // Draws one user's page. Used by show(), and by update() when the form has errors
    private function renderUserPage(array $user, array $errors = [], ?array $old = null): void
    {
        // 1.
        $fullName = $user['first_name'] . ' ' . $user['last_name'];

        // 2.
        $members = new GroupMemberRepository();
        $groups = $members->findAllForAdminByUser((int) $user['user_id']);

        // 3.
        $usersMessage = Session::takeFlash('admin_users_message');
        $usersError = Session::takeFlash('admin_users_error');

        // 4.
        $old = $old ?? $user;

        // 5.
        View::render('admin-user', [
            'pageTitle' => 'Administration · ' . $fullName,
            'pageStyle' => 'admin',
            'pageScripts' => ['confirm'],
            'isLoggedIn' => true,
            'isOperator' => true,
            'adminPage' => 'users',
            'user' => $user,
            'groups' => $groups,
            'usersMessage' => $usersMessage,
            'usersError' => $usersError,
            'errors' => $errors,
            'old' => $old,
        ]);
    }

    // Shows one user's page: /admin/user?id=7
    public function show(): void
    {
        Auth::requireOperator();

        // The id from the address. A broken id (abc, -1, empty) becomes null
        $userId = queryId('id');
        if ($userId === null) {
            abort(400, 'Ugyldigt bruger-ID.');
        }

        $users = new UserRepository();
        $user = $users->findForAdmin($userId);

        // A real number, but no user has it
        if ($user === null) {
            abort(404, 'Brugeren blev ikke fundet.');
        }

        $this->renderUserPage($user);
    }

    // Blocks or unblocks one user: the buttons in the list.
    public function updateStatus(): void
    {
        Auth::requireOperator();

        $userId = inputId('user_id');
        $action = input('action');

        // The only two things the buttons may ask for: action => [from status, to status]
        $changes = [
            'block' => ['active', 'blocked'],
            'unblock' => ['blocked', 'active'],
        ];

        // A broken id or an unknown action never comes from our own page
        if ($userId === null || !isset($changes[$action])) {
            abort(400, 'Ugyldig handling.');
        }

        // Unpack the pair into two variables
        [$fromStatus, $toStatus] = $changes[$action];

        $users = new UserRepository();
        $changed = $users->updateStatus($userId, $fromStatus, $toStatus);

        if ($changed === 1) {
            Session::flash('admin_users_message', $action === 'block' ? 'Brugeren er blokeret.' : 'Blokeringen er fjernet.');
        } else {
            Session::flash('admin_users_error', 'Status kunne ikke ændres. Brugeren har allerede en anden status.');
        }

        // Back to where the button was: this user's page, or the list
        $backTo = input('from') === 'user' ? '/admin/user?id=' . $userId : '/admin/users';
        redirect($backTo);
    }

    public function update(): void
    {
        Auth::requireOperator();

        $userId = inputId('user_id');
        if ($userId === null) {
            abort(400, 'Ugyldigt bruger-ID.');
        }

        $users = new UserRepository();
        $user = $users->findForAdmin($userId);
        if ($user === null) {
            abort(404, 'Brugeren blev ikke fundet.');
        }

        if ((int) $user['is_operator'] === 1) {
            abort(403, 'En operatør kan ikke ændres her.');
        }

        $firstName = input('first_name');
        $lastName = input('last_name');
        $username = input('username');
        $email = mb_strtolower(input('email'));
        $birthdate = input('birthdate');

        $validator = new UserValidator();
        $errors = $validator->validateDetails($firstName, $lastName, $username, $email, $birthdate, $userId);
        if ($errors !== []) {
            http_response_code(422);
            $this->renderUserPage(
                $user,
                $errors,
                [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'username' => $username,
                    'email' => $email,
                    'birthdate' => $birthdate,
                ]
            );
            return;
        }

        $users->updateDetails($userId, $firstName, $lastName, $username, $email, $birthdate);

        if ($email !== $user['email']) {
            $users->clearEmailVerified($userId);
        }

        Session::flash('admin_users_message', 'Brugerens oplysninger er gemt.');
        redirect('/admin/user?id=' . $userId);
    }

    public function removePicture(): void
    {
        // 1.
        Auth::requireOperator();

        // 2.
        $userId = inputId('user_id');
        if ($userId === null) {
            abort(400, 'Ugyldigt bruger-ID.');
        }

        // 3.
        $users = new UserRepository();
        $user = $users->findForAdmin($userId);
        if ($user === null) {
            abort(404, 'Brugeren blev ikke fundet.');
        }

        // 4.
        if ((int) $user['is_operator'] === 1) {
            abort(403, 'En operatørs billede kan ikke fjernes her.');
        }

        // 5.
        if ($user['profile_image'] !== null) {
            $users->updateProfileImage($userId, null, null, null);

            try {
                $cloudinary = new CloudinaryService();
                $cloudinary->delete($user['profile_image']);
            } catch (\RuntimeException $e) {
                error_log('Removed profile picture not deleted: ' . $e->getMessage());
            }
        }

        // 6.
        Session::flash('admin_users_message', 'Profilbilledet er fjernet.');
        redirect('/admin/user?id=' . $userId);
    }
}
