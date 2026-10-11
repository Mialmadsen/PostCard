<?php
$pageTitle = 'Administration · Oversigt';
$isOperator = true;
$pageStyle = 'admin';
$adminPage = 'overview';
require __DIR__ . '/../views/partials/header.php';
?>

<!-- Every admin-*.php page (except admin-login.php) is only for a logged-in operator.
     Anyone else is sent to admin-login.php. The server checks this on every request. -->

<main id="main" class="site-main">
    <div class="container">
        <p class="eyebrow">Administration</p>
        <h1>Oversigt</h1>

        <div class="sidebar-layout">
            <?php require __DIR__ . '/../views/partials/admin-nav.php'; ?>

            <div class="flow">
                <section class="card" aria-labelledby="numbers-heading">
                    <h2 class="section-title" id="numbers-heading"><span class="brand">PostCard</span> i tal</h2>
                    <dl class="stat-list">
                        <div class="stat-list__item">
                            <dt>Brugere</dt>
                            <dd class="stat-list__number">214</dd>
                        </div>
                        <div class="stat-list__item">
                            <dt>Grupper</dt>
                            <dd class="stat-list__number">38</dd>
                        </div>
                        <div class="stat-list__item">
                            <dt>Opslag</dt>
                            <dd class="stat-list__number">1.926</dd>
                        </div>
                        <div class="stat-list__item">
                            <dt>Blokerede brugere</dt>
                            <dd class="stat-list__number">3</dd>
                        </div>
                    </dl>
                </section>

                <section class="card" aria-labelledby="new-users-heading">
                    <h2 class="section-title" id="new-users-heading">Nyeste brugere</h2>

                    <!-- LOOP: the 5 newest users (users.created_at) -->
                    <ul class="divided-list">
                        <li>
                            <p><a href="admin-user.php?id=214">Morten Holm</a></p>
                            <p class="meta">Oprettet <time datetime="2026-10-11">11. okt. 2026</time> · Aktiv</p>
                        </li>
                        <li>
                            <p><a href="admin-user.php?id=213">Ida Larsen</a></p>
                            <p class="meta">Oprettet <time datetime="2026-10-10">10. okt. 2026</time> · Aktiv</p>
                        </li>
                        <li>
                            <p><a href="admin-user.php?id=212">Bo Larsen</a></p>
                            <p class="meta">Oprettet <time datetime="2026-10-09">9. okt. 2026</time> · Aktiv</p>
                        </li>
                        <li>
                            <p><a href="admin-user.php?id=211">Freja Nielsen</a></p>
                            <p class="meta">Oprettet <time datetime="2026-10-08">8. okt. 2026</time> · Aktiv</p>
                        </li>
                        <li>
                            <p><a href="admin-user.php?id=210">Søren Madsen</a></p>
                            <p class="meta">Oprettet <time datetime="2026-10-06">6. okt. 2026</time> · Aktiv</p>
                        </li>
                    </ul>
                    <!-- /LOOP -->

                    <p><a href="admin-users.php">Se alle brugere</a></p>
                </section>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../views/partials/footer.php'; ?>
