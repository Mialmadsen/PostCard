<?php
// The controller shows this page with:
// View::render('admin-users', ['pageTitle' => 'Administration · Brugere', 'isOperator' => true, 'pageStyle' => 'admin', 'pageScripts' => ['confirm'], 'adminPage' => 'users']);
?>

<main id="main" class="site-main">
    <div class="container">
        <p class="eyebrow">Administration</p>
        <h1>Brugere</h1>

        <div class="sidebar-layout">
            <?php require __DIR__ . '/../partials/admin-nav.php'; ?>

            <section class="card flow" aria-labelledby="users-heading">
                <h2 class="visually-hidden" id="users-heading">Alle brugere</h2>

                <!-- GET, not POST: searching changes nothing, and the result can be bookmarked -->
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

                <p class="meta">214 brugere</p>

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
                            <!-- LOOP: users matching the search, newest first (users.created_at), 50 per page.
                                 Blokér: user_status = 'blocked' (cannot log in). Fjern blokering: back to 'active'.
                                 The operator's own row has no button. -->
                            <tr>
                                <td><a href="<?= url('/admin/user?id=214') ?>">Morten Holm</a></td>
                                <td>morten@example.com</td>
                                <td>Aktiv</td>
                                <td><time datetime="2026-10-11">11. okt. 2026</time></td>
                                <td>
                                    <form class="js-confirm" action="<?= url('/admin/users') ?>" method="post"
                                        data-confirm="Vil du blokere Morten Holm? Brugeren kan ikke logge ind, før du fjerner blokeringen.">
                                        <?= \PostCard\Core\Csrf::field() ?>
                                        <input type="hidden" name="user_id" value="214">
                                        <input type="hidden" name="action" value="block">
                                        <button class="button button--secondary" type="submit">Blokér <span class="visually-hidden">Morten Holm</span></button>
                                    </form>
                                </td>
                            </tr>
                            <tr>
                                <td><a href="<?= url('/admin/user?id=213') ?>">Ida Larsen</a></td>
                                <td>ida@example.com</td>
                                <td>Aktiv</td>
                                <td><time datetime="2026-10-10">10. okt. 2026</time></td>
                                <td>
                                    <form class="js-confirm" action="<?= url('/admin/users') ?>" method="post"
                                        data-confirm="Vil du blokere Ida Larsen? Brugeren kan ikke logge ind, før du fjerner blokeringen.">
                                        <?= \PostCard\Core\Csrf::field() ?>
                                        <input type="hidden" name="user_id" value="213">
                                        <input type="hidden" name="action" value="block">
                                        <button class="button button--secondary" type="submit">Blokér <span class="visually-hidden">Ida Larsen</span></button>
                                    </form>
                                </td>
                            </tr>
                            <tr>
                                <td><a href="<?= url('/admin/user?id=198') ?>">Kasper Friis</a></td>
                                <td>kasper@example.com</td>
                                <td>Blokeret</td>
                                <td><time datetime="2026-09-28">28. sep. 2026</time></td>
                                <td>
                                    <form action="<?= url('/admin/users') ?>" method="post">
                                        <?= \PostCard\Core\Csrf::field() ?>
                                        <input type="hidden" name="user_id" value="198">
                                        <input type="hidden" name="action" value="unblock">
                                        <button class="button button--secondary" type="submit">Fjern blokering <span class="visually-hidden">af Kasper Friis</span></button>
                                    </form>
                                </td>
                            </tr>
                            <tr>
                                <td><a href="<?= url('/admin/user?id=150') ?>">Lene Bach</a></td>
                                <td>lene@example.com</td>
                                <td>Slettet</td>
                                <td><time datetime="2026-09-17">17. sep. 2026</time></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td><a href="<?= url('/admin/user?id=1') ?>">Mia Nielsen</a></td>
                                <td>mia@example.com</td>
                                <td>Aktiv</td>
                                <td><time datetime="2026-09-01">1. sep. 2026</time></td>
                                <td>
                                    <form class="js-confirm" action="<?= url('/admin/users') ?>" method="post"
                                        data-confirm="Vil du blokere Mia Nielsen? Brugeren kan ikke logge ind, før du fjerner blokeringen.">
                                        <?= \PostCard\Core\Csrf::field() ?>
                                        <input type="hidden" name="user_id" value="1">
                                        <input type="hidden" name="action" value="block">
                                        <button class="button button--secondary" type="submit">Blokér <span class="visually-hidden">Mia Nielsen</span></button>
                                    </form>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- IF more than 50 results: simple page links, no infinite scroll
                <nav aria-label="Sider"><a href="<?= url('/admin/users?page=2') ?>">Næste side</a></nav> -->
            </section>
        </div>
    </div>
</main>
