<?php

/** @var array $group */
/** @var array $members */
/** @var int $currentUserId */
/** @var bool $isAdmin */


?>

<main id="main" class="site-main">
    <div class="container container--text">
        <nav class="breadcrumb" aria-label="Du er her">
            <ol class="breadcrumb__list">
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="<?= url('/home') ?>">Mine grupper</a>
                </li>
                <li class="breadcrumb__item"><a class="breadcrumb__link"
                        href="<?= url('/group?id=' . $group['group_id']) ?>"><?= e($group['name']) ?></a></li>
                <li class="breadcrumb__item"><a class="breadcrumb__link"
                        href="<?= url('/members?group_id=' . $group['group_id']) ?>" aria-current="page">Medlemmer</a>
                </li>
            </ol>
        </nav>

        <header class="page-header">
            <p class="group-label"><?= e($group['name']) ?></p>
            <h1>Medlemmer</h1>
            <p><?= count($members) === 1 ? '1 medlem' : count($members) . ' medlemmer' ?>. Kun de kan se gruppens
                oplevelser
                og opslag.</p>
            <!-- IF member_role = 'group_admin' -->
            <?php if ($isAdmin): ?>
                <p><a class="button button--secondary" href="<?= url('/invite?group_id=' . $group['group_id']) ?>">Inviter
                        nye medlemmer</a></p>
            <?php endif; ?>

        </header>

        <div class="card-stack">
            <section class="card" aria-labelledby="members-heading">
                <h2 class="visually-hidden" id="members-heading">Alle medlemmer</h2>

                <ul class="divided-list">
                    <?php foreach ($members as $member): ?>
                        <?php $fullName = $member['first_name'] . ' ' . $member['last_name']; ?>
                        <li class="member">
                            <span class="avatar" aria-hidden="true"><?= e(initials($member['first_name'], $member['last_name'])) ?></span>
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
                                    <time datetime="<?= e($member['joined_at']) ?>"><?= danishDate($member['joined_at']) ?></time>
                                </p>
                            </div>

                            <!-- Buttons only for admins, and never on an admin's row. The server checks this again. -->
                            <?php if ($isAdmin && $member['member_role'] !== 'group_admin'): ?>
                                <div class="button-row member__actions">
                                    <form class="js-confirm" action="<?= url('/make-admin') ?>" method="post"
                                        data-confirm="Vil du gøre <?= e($fullName) ?> til admin? Admins kan godkende oplevelser, invitere og fjerne medlemmer.">
                                        <?= \PostCard\Core\Csrf::field() ?>
                                        <input type="hidden" name="group_id" value="<?= $group['group_id'] ?>">
                                        <input type="hidden" name="user_id" value="<?= $member['user_id'] ?>">
                                        <button class="button button--secondary" type="submit">Gør <span
                                                class="visually-hidden"><?= e($fullName) ?></span> til admin</button>
                                    </form>
                                    <form class="js-confirm" action="<?= url('/remove-member') ?>" method="post"
                                        data-confirm="Vil du fjerne <?= e($fullName) ?> fra <?= e($group['name']) ?>? Det, der er delt, bliver i gruppens album.">
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