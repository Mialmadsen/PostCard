<?php

declare(strict_types=1);

namespace PostCard\Controllers;

use PostCard\Core\Auth;
use PostCard\Core\View;
use PostCard\Repositories\UserRepository;
use PostCard\Core\Session;

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

        redirect('/admin/users');
    }
}
