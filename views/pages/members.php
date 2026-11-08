<?php

/** @var array $group */
/** @var array $members */
/** @var int $currentUserId */
/** @var bool $isAdmin */
/** @var string|null $memberMessage the success message after a redirect, or null */
/** @var string|null $memberError   the failure message after a redirect, or null */



?>

<main id="main" class="site-main">
    <div class="container container--text">
        <?php
        $breadcrumbs = [
            ['label' => 'Mine grupper', 'path' => '/home'],
            ['label' => $group['name'], 'path' => '/group?id=' . $group['group_id']],
            ['label' => 'Medlemmer', 'path' => '/members?group_id=' . $group['group_id']],
        ];
        require __DIR__ . '/../partials/breadcrumbs.php';
        ?>

        <header class="page-header">
            <p class="group-label"><?= e($group['name']) ?></p>
            <h1>Medlemmer</h1>
            <p><?= count($members) === 1 ? '1 medlem' : count($members) . ' medlemmer' ?>. Kun de kan se gruppens
                oplevelser
                og opslag.</p>
            <?php /* IF member_role = 'group_admin' */ ?>
            <?php if ($isAdmin): ?>
                <p><a class="button button--secondary" href="<?= url('/invite?group_id=' . $group['group_id']) ?>">Inviter
                        nye medlemmer</a></p>
            <?php endif; ?>

        </header>
        <?php if ($memberMessage !== null): ?>
            <?php
            $flashMessage = $memberMessage;
            $flashType = 'success';
            $flashLink = null;
            require __DIR__ . '/../partials/flash.php';
            ?>
        <?php endif; ?>
        <?php if ($memberError !== null): ?>
            <?php
            $flashMessage = $memberError;
            $flashType = 'error';
            $flashLink = null;
            require __DIR__ . '/../partials/flash.php';
            ?>
        <?php endif; ?>

        <div class="card-stack">
            <section class="card" aria-labelledby="members-heading">
                <h2 class="visually-hidden" id="members-heading">Alle medlemmer</h2>

                <ul class="divided-list">
                    <?php foreach ($members as $member): ?>
                        <?php $fullName = $member['first_name'] . ' ' . $member['last_name']; ?>
                        <li class="member">
                            <?php
                            $avatarUser = $member;
                            $avatarClass = '';
                            require __DIR__ . '/../partials/avatar.php';
                            ?>
                            <div>
                                <p class="member__name">
                                    <?= e($fullName) ?>
                                    <?php if ($member['user_id'] === $currentUserId): ?>
                                        <span class="meta">(dig)</span>
                                    <?php endif; ?>
                                </p>
                                <p class="meta">
                                    <?php if ($member['member_role'] === 'group_admin'): ?>
                                        Admin · medlem siden
                                    <?php else: ?>
                                        Medlem siden
                                    <?php endif; ?>
                                    <time
                                        datetime="<?= e($member['joined_at']) ?>"><?= danishDate($member['joined_at']) ?></time>
                                </p>
                            </div>

                            <?php /* Buttons only for admins, and never on an admin's row. The server checks this again. */ ?>
                            <?php if ($isAdmin && $member['member_role'] !== 'group_admin'): ?>
                                <div class="button-row member__actions">
                                    <form class="js-confirm" action="<?= url('/make-admin') ?>" method="post"
                                        data-confirm="Vil du gøre <?= e($fullName) ?> til admin? Admins kan godkende oplevelser, invitere og fjerne medlemmer." data-confirm-button="Ja, gør til admin">
                                        <?= \PostCard\Core\Csrf::field() ?>
                                        <input type="hidden" name="group_id" value="<?= $group['group_id'] ?>">
                                        <input type="hidden" name="user_id" value="<?= $member['user_id'] ?>">
                                        <button class="button button--secondary" type="submit">Gør <span
                                                class="visually-hidden"><?= e($fullName) ?></span> til admin</button>
                                    </form>
                                    <form class="js-confirm" action="<?= url('/remove-member') ?>" method="post"
                                        data-confirm="Vil du fjerne <?= e($fullName) ?> fra <?= e($group['name']) ?>? Det, der er delt, bliver i gruppens album." data-confirm-button="Ja, fjern">
                                        <?= \PostCard\Core\Csrf::field() ?>
                                        <input type="hidden" name="group_id" value="<?= $group['group_id'] ?>">
                                        <input type="hidden" name="user_id" value="<?= $member['user_id'] ?>">
                                        <button class="button button--secondary" type="submit">Fjern <span
                                                class="visually-hidden"><?= e($fullName) ?></span></button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </section>
        </div>
    </div>
</main>