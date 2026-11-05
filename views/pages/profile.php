<?php
// The controller shows this page with:
// View::render('profile', ['pageTitle' => 'Min profil', 'isLoggedIn' => true, 'pageStyle' => 'profile']);

/** @var array $user */
/** @var array $groups */
/** @var array $stats */
?>

<main id="main" class="site-main">
    <div class="container">
        <p class="eyebrow">Min profil</p>
        <h1><?= e($user['first_name']) ?> <?= e($user['last_name']) ?></h1>
        <p>Dit eget album i tal. Tallene viser kun dine egne oplevelser – vi sammenligner dig aldrig med andre.</p>

        <div class="sidebar-layout">

            <!-- Left column: who I am (users table) -->
            <section class="card flow profile-card" aria-labelledby="about-heading">
                <h2 class="visually-hidden" id="about-heading">Om mig</h2>
                <!-- IF users.profile_image: <img class="avatar avatar--large" src="…" alt=""> ELSE initials -->
                <span class="avatar avatar--large"
                    aria-hidden="true"><?= e(initials($user['first_name'], $user['last_name'])) ?></span>
                <p class="meta">@<?= e($user['username']) ?></p>
                <p><a class="button button--secondary" href="<?= url('/settings') ?>">Rediger profil</a></p>
            </section>

            <!-- Right column: the infographic -->
            <div class="flow">

                <section class="card" aria-labelledby="numbers-heading">
                    <h2 class="section-title" id="numbers-heading">Mit album i tal</h2>

                    <!-- Counts for the logged-in user only -->
                    <dl class="stat-list">
                        <div class="stat-list__item">
                            <dt>Grupper</dt>
                            <dd class="stat-list__number"><?= count($groups) ?></dd>

                        </div>
                        <div class="stat-list__item">
                            <dt>Oplevelser</dt>
                            <dd class="stat-list__number"><?= (int) $stats['experience_count'] ?></dd>
                        </div>
                        <div class="stat-list__item">
                            <dt>Opslag</dt>
                            <dd class="stat-list__number"><?= (int) $stats['post_count'] ?></dd>
                        </div>
                        <div class="stat-list__item">
                            <dt>Billeder</dt>
                            <dd class="stat-list__number"><?= (int) $stats['image_count'] ?></dd>
                        </div>
                    </dl>

                    <h3 class="profile-subheading">Modtagne reaktioner</h3>
                    <!-- Likes and comments other members gave on my posts -->
                    <dl class="stat-list">
                        <div class="stat-list__item">
                            <dt>Synes godt om</dt>
                            <dd class="stat-list__number"><?= (int) $stats['like_count'] ?></dd>
                        </div>
                        <div class="stat-list__item">
                            <dt>Kommentarer</dt>
                            <dd class="stat-list__number"><?= (int) $stats['comment_count'] ?></dd>
                        </div>
                    </dl>
                </section>

                <section class="card" aria-labelledby="timeline-heading">
                    <h2 class="section-title" id="timeline-heading">Opslag pr. måned</h2>
                    <p class="meta">De seneste 12 måneder.</p>

                    <!-- LOOP: the last 12 months, oldest first.
                         --posts = posts that month, --max (on the list) = the highest month,
                         so the tallest bar always fills the chart. -->
                    <ol class="month-chart" style="--max: 5">
                        <li class="month-chart__month" style="--posts: 0">
                            <span class="month-chart__count" aria-hidden="true">0</span>
                            <span class="month-chart__bar" aria-hidden="true"></span>
                            <span class="month-chart__label" aria-hidden="true">nov</span>
                            <span class="visually-hidden">November 2025: 0 opslag</span>
                        </li>
                        <li class="month-chart__month" style="--posts: 5">
                            <span class="month-chart__count" aria-hidden="true">5</span>
                            <span class="month-chart__bar" aria-hidden="true"></span>
                            <span class="month-chart__label" aria-hidden="true">dec</span>
                            <span class="visually-hidden">December 2025: 5 opslag</span>
                        </li>
                        <li class="month-chart__month" style="--posts: 0">
                            <span class="month-chart__count" aria-hidden="true">0</span>
                            <span class="month-chart__bar" aria-hidden="true"></span>
                            <span class="month-chart__label" aria-hidden="true">jan</span>
                            <span class="visually-hidden">Januar 2026: 0 opslag</span>
                        </li>
                        <li class="month-chart__month" style="--posts: 0">
                            <span class="month-chart__count" aria-hidden="true">0</span>
                            <span class="month-chart__bar" aria-hidden="true"></span>
                            <span class="month-chart__label" aria-hidden="true">feb</span>
                            <span class="visually-hidden">Februar 2026: 0 opslag</span>
                        </li>
                        <li class="month-chart__month" style="--posts: 4">
                            <span class="month-chart__count" aria-hidden="true">4</span>
                            <span class="month-chart__bar" aria-hidden="true"></span>
                            <span class="month-chart__label" aria-hidden="true">mar</span>
                            <span class="visually-hidden">Marts 2026: 4 opslag</span>
                        </li>
                        <li class="month-chart__month" style="--posts: 0">
                            <span class="month-chart__count" aria-hidden="true">0</span>
                            <span class="month-chart__bar" aria-hidden="true"></span>
                            <span class="month-chart__label" aria-hidden="true">apr</span>
                            <span class="visually-hidden">April 2026: 0 opslag</span>
                        </li>
                        <li class="month-chart__month" style="--posts: 3">
                            <span class="month-chart__count" aria-hidden="true">3</span>
                            <span class="month-chart__bar" aria-hidden="true"></span>
                            <span class="month-chart__label" aria-hidden="true">maj</span>
                            <span class="visually-hidden">Maj 2026: 3 opslag</span>
                        </li>
                        <li class="month-chart__month" style="--posts: 0">
                            <span class="month-chart__count" aria-hidden="true">0</span>
                            <span class="month-chart__bar" aria-hidden="true"></span>
                            <span class="month-chart__label" aria-hidden="true">jun</span>
                            <span class="visually-hidden">Juni 2026: 0 opslag</span>
                        </li>
                        <li class="month-chart__month" style="--posts: 0">
                            <span class="month-chart__count" aria-hidden="true">0</span>
                            <span class="month-chart__bar" aria-hidden="true"></span>
                            <span class="month-chart__label" aria-hidden="true">jul</span>
                            <span class="visually-hidden">Juli 2026: 0 opslag</span>
                        </li>
                        <li class="month-chart__month" style="--posts: 0">
                            <span class="month-chart__count" aria-hidden="true">0</span>
                            <span class="month-chart__bar" aria-hidden="true"></span>
                            <span class="month-chart__label" aria-hidden="true">aug</span>
                            <span class="visually-hidden">August 2026: 0 opslag</span>
                        </li>
                        <li class="month-chart__month" style="--posts: 4">
                            <span class="month-chart__count" aria-hidden="true">4</span>
                            <span class="month-chart__bar" aria-hidden="true"></span>
                            <span class="month-chart__label" aria-hidden="true">sep</span>
                            <span class="visually-hidden">September 2026: 4 opslag</span>
                        </li>
                        <li class="month-chart__month" style="--posts: 2">
                            <span class="month-chart__count" aria-hidden="true">2</span>
                            <span class="month-chart__bar" aria-hidden="true"></span>
                            <span class="month-chart__label" aria-hidden="true">okt</span>
                            <span class="visually-hidden">Oktober 2026: 2 opslag</span>
                        </li>
                    </ol>
                    <!-- /LOOP -->
                </section>

                <section class="card" aria-labelledby="my-groups-heading">
                    <h2 class="section-title" id="my-groups-heading">Mine grupper og oplevelser</h2>

                    <!-- One row per group from v_user_groups. My experiences per group come in step 4 -->
                    <ul class="divided-list">
                        <?php foreach ($groups as $group): ?>
                            <li class="flow">
                                <h3><a href="<?= url('/group?id=' . $group['group_id']) ?>"><?= e($group['name']) ?></a>
                                </h3>
                                <p class="meta">
                                    <?= $group['member_role'] === 'group_admin' ? 'Admin' : 'Medlem' ?>
                                    · <?= (int) $group['member_count'] ?>
                                    <?= (int) $group['member_count'] === 1 ? 'medlem' : 'medlemmer' ?>
                                </p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>

            </div>
        </div>
    </div>
</main>