<?php
// The controller shows this page with:
// View::render('admin-users', ['pageTitle' => 'Administration · Brugere', 'isOperator' => true, 'pageStyle' => 'admin', 'pageScripts' => ['confirm'], 'adminPage' => 'users', 'users' => [...]]);

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

                <?php /* GET, not POST: searching changes nothing, and the result can be bookmarked */ ?>
                <form class="filter-bar" action="<?= url('/admin/users') ?>" method="get" role="search">
                    <div class="field">
                        <label class="field__label" for="search">Søg efter navn eller e-mail</label>
                        <input class="field__input" id="search" name="q" type="search">
                    </div>
                    <div class="field">
                        <label class="field__label" for="status">Status</label>
                        <select class="field__input" id="status" name="status">
                            <option value="">Alle</option>
                            <option value="active">Aktive</option>
                            <option value="blocked">Blokerede</option>
                            <option value="deleted">Slettede</option>
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
