<?php
// The controller shows this page with:
// View::render('admin', ['pageTitle' => 'Administration · Oversigt', 'isOperator' => true, 'pageStyle' => 'admin', 'adminPage' => 'overview', 'counts' => [...], 'newestUsers' => [...]]);

// The four numbers, in the order they are shown: label => key from findPlatformCounts()
$statItems = [
    'Brugere' => 'user_count',
    'Grupper' => 'group_count',
    'Opslag' => 'post_count',
    'Blokerede brugere' => 'blocked_count',
];

// The database's status words, as the operator reads them
$statusLabels = [
    'active' => 'Aktiv',
    'blocked' => 'Blokeret',
    'deleted' => 'Slettet',
];
?>

<?php /* Every /admin page is only for a logged-in operator. The controller checks this on every
     request (Auth::requireOperator): a guest is sent to /login, a normal user gets 403. */ ?>

<main id="main" class="site-main">
    <div class="container">
        <p class="eyebrow">Administration</p>
        <h1>Oversigt</h1>

        <div class="sidebar-layout">
            <?php require __DIR__ . '/../partials/admin-nav.php'; ?>

            <div class="flow">
                <section class="card" aria-labelledby="numbers-heading">
                    <h2 class="section-title" id="numbers-heading"><span class="brand">PostCard</span> i tal</h2>
                    <?php /* One box per number. number_format(…, 0, ',', '.') writes 1926 the Danish way: 1.926 */ ?>
                    <dl class="stat-list">
                        <?php foreach ($statItems as $label => $key): ?>
                            <div class="stat-list__item">
                                <dt><?= e($label) ?></dt>
                                <dd class="stat-list__number"><?= number_format((int) $counts[$key], 0, ',', '.') ?></dd>
                            </div>
                        <?php endforeach; ?>
                    </dl>
                </section>

                <section class="card" aria-labelledby="new-users-heading">
                    <h2 class="section-title" id="new-users-heading">Nyeste brugere</h2>

                    <?php /* The 5 newest accounts (findNewest), newest first */ ?>
                    <ul class="divided-list">
                        <?php foreach ($newestUsers as $user): ?>
                            <li>
                                <p><a href="<?= url('/admin/user?id=' . (int) $user['user_id']) ?>"><?= e($user['first_name'] . ' ' . $user['last_name']) ?></a></p>
                                <p class="meta">Oprettet <time datetime="<?= e(substr($user['created_at'], 0, 10)) ?>"><?= e(danishDate($user['created_at'])) ?></time> · <?= e($statusLabels[$user['user_status']]) ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <p><a href="<?= url('/admin/users') ?>">Se alle brugere</a></p>
                </section>
            </div>
        </div>
    </div>
</main>
