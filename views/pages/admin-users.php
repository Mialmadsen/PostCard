<?php
// The controller shows this page with:
// View::render('admin-users', ['pageTitle' => 'Administration · Brugere', 'isOperator' => true, 'pageStyle' => 'admin', 'pageScripts' => ['confirm'], 'adminPage' => 'users', 'users' => [...], 'search' => '', 'selectedStatus' => '', 'usersMessage' => null, 'usersError' => null]);

// The database's status words, as the operator reads them
$statusLabels = [
    'active' => 'Aktiv',
    'blocked' => 'Blokeret',
    'deleted' => 'Slettet',
];
?>

<main id="main" class="site-main">
    <div class="container">
        <p class="eyebrow">Administration</p>
        <h1>Brugere</h1>

        <div class="sidebar-layout">
            <?php require __DIR__ . '/../partials/admin-nav.php'; ?>

            <section class="card flow" aria-labelledby="users-heading">
                <h2 class="visually-hidden" id="users-heading">Alle brugere</h2>

                <?php /* After Blokér / Fjern blokering: the seagull pop-up, success or error */ ?>
                <?php if ($usersMessage !== null): ?>
                    <?php
                    $flashMessage = $usersMessage;
                    $flashType = 'success';
                    $flashLink = null;
                    require __DIR__ . '/../partials/flash.php';
                    ?>
                <?php endif; ?>
                <?php if ($usersError !== null): ?>
                    <?php
                    $flashMessage = $usersError;
                    $flashType = 'error';
                    $flashLink = null;
                    require __DIR__ . '/../partials/flash.php';
                    ?>
                <?php endif; ?>

                <?php /* GET, not POST: searching changes nothing, and the result can be bookmarked */ ?>
                <form class="filter-bar" action="<?= url('/admin/users') ?>" method="get" role="search">
                    <div class="field">
                        <label class="field__label" for="search">Søg efter navn eller e-mail</label>
                        <?php /* e(): the visitor's own text is printed back into the page (reflected XSS) */ ?>
                        <input class="field__input" id="search" name="q" type="search" value="<?= e($search) ?>">
                    </div>
                    <div class="field">
                        <label class="field__label" for="status">Status</label>
                        <?php /* The chosen status stays picked after the search; "Alle" is shown when none is */ ?>
                        <select class="field__input" id="status" name="status">
                            <option value="">Alle</option>
                            <option value="active" <?= $selectedStatus === 'active' ? 'selected' : '' ?>>Aktive</option>
                            <option value="blocked" <?= $selectedStatus === 'blocked' ? 'selected' : '' ?>>Blokerede</option>
                            <option value="deleted" <?= $selectedStatus === 'deleted' ? 'selected' : '' ?>>Slettede</option>
                        </select>
                    </div>
                    <button class="button button--secondary" type="submit">Søg</button>
                </form>

                <p class="meta"><?= count($users) ?> brugere</p>

                <div class="table-wrap" tabindex="0" role="region" aria-labelledby="users-caption">
                    <table class="data-table">
                        <caption class="visually-hidden" id="users-caption">Brugere, nyeste først</caption>
                        <thead>
                            <tr>
                                <th scope="col">Navn</th>
                                <th scope="col">E-mail</th>
                                <th scope="col">Status</th>
                                <th scope="col">Oprettet</th>
                                <th scope="col"><span class="visually-hidden">Handling</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php /* One row per user, newest first. Blokér sets user_status = 'blocked':
                                 the user is logged out on the next click and cannot log in again.
                                 Fjern blokering sets it back to 'active'. Operators and deleted users have no button. */ ?>
                            <?php foreach ($users as $user): ?>
                                <?php $fullName = $user['first_name'] . ' ' . $user['last_name']; ?>
                                <tr>
                                    <td><a href="<?= url('/admin/user?id=' . (int) $user['user_id']) ?>"><?= e($fullName) ?></a></td>
                                    <td><?= e($user['email']) ?></td>
                                    <td><?= e($statusLabels[$user['user_status']]) ?></td>
                                    <td><time datetime="<?= e(substr($user['created_at'], 0, 10)) ?>"><?= e(danishDate($user['created_at'])) ?></time></td>
                                    <td>
                                        <?php if ((int) $user['is_operator'] === 1): ?>
                                            <span class="meta">Operatør</span>
                                        <?php elseif ($user['user_status'] === 'active'): ?>
                                            <form class="js-confirm" action="<?= url('/admin/users') ?>" method="post"
                                                data-confirm="Vil du blokere <?= e($fullName) ?>? Brugeren bliver logget ud og kan ikke logge ind, før du fjerner blokeringen."
                                                data-confirm-button="Blokér">
                                                <?= \PostCard\Core\Csrf::field() ?>
                                                <input type="hidden" name="user_id" value="<?= (int) $user['user_id'] ?>">
                                                <input type="hidden" name="action" value="block">
                                                <button class="button button--secondary" type="submit">Blokér <span class="visually-hidden"><?= e($fullName) ?></span></button>
                                            </form>
                                        <?php elseif ($user['user_status'] === 'blocked'): ?>
                                            <form action="<?= url('/admin/users') ?>" method="post">
                                                <?= \PostCard\Core\Csrf::field() ?>
                                                <input type="hidden" name="user_id" value="<?= (int) $user['user_id'] ?>">
                                                <input type="hidden" name="action" value="unblock">
                                                <button class="button button--secondary" type="submit">Fjern blokering <span class="visually-hidden">af <?= e($fullName) ?></span></button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php /* IF more than 50 results: simple page links, no infinite scroll
                <nav aria-label="Sider"><a href="<?= url('/admin/users?page=2') ?>">Næste side</a></nav> */ ?>
            </section>
        </div>
    </div>
</main>
