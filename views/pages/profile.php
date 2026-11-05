<?php
// The controller shows this page with:
// View::render('profile', ['pageTitle' => 'Min profil', 'isLoggedIn' => true, 'pageStyle' => 'profile']);

/** @var array $user */
/** @var array $groups */
/** @var array $stats */
/** @var array $experiencesByGroup  my experiences per group_id, each with statusText */
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
                <?php if ($user['bio'] !== null && $user['bio'] !== ''): ?>
                    <p><?= e($user['bio']) ?></p>
                <?php endif; ?>
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

                <section class="card" aria-labelledby="my-groups-heading">
                    <h2 class="section-title" id="my-groups-heading">Mine grupper og oplevelser</h2>

                    <?php if ($groups === []): ?>
                        <p class="meta">Du er ikke medlem af nogen grupper endnu.</p>
                        <p><a class="button button--secondary" href="<?= url('/create-group') ?>">Opret en gruppe</a></p>
                    <?php else: ?>

                    <!-- One row per group from v_user_groups, with my experiences in that group under it -->
                    <ul class="divided-list">
                        <?php foreach ($groups as $group): ?>
                            <?php
                            // My experiences in this group, or none
                            $groupExperiences = $experiencesByGroup[$group['group_id']] ?? [];
                            ?>
                            <li class="flow">
                                <h3><a href="<?= url('/group?id=' . $group['group_id']) ?>"><?= e($group['name']) ?></a>
                                </h3>
                                <p class="meta">
                                    <?= $group['member_role'] === 'group_admin' ? 'Admin' : 'Medlem' ?>
                                    · <?= (int) $group['member_count'] ?>
                                    <?= (int) $group['member_count'] === 1 ? 'medlem' : 'medlemmer' ?>
                                </p>

                                <?php if ($groupExperiences !== []): ?>
                                    <ul class="profile-experiences">
                                        <?php foreach ($groupExperiences as $experience): ?>
                                            <?php
                                            // Only approved and closed experiences have a page with posts
                                            $isInAlbum = in_array($experience['experience_status'], ['approved', 'closed'], true);
                                            ?>
                                            <li>
                                                <?php if ($isInAlbum): ?>
                                                    <a href="<?= url('/experience?id=' . $experience['experience_id']) ?>"><?= e($experience['name']) ?></a>
                                                <?php else: ?>
                                                    <?= e($experience['name']) ?>
                                                <?php endif; ?>
                                                <span class="meta">
                                                    · <?= e($experience['statusText']) ?>
                                                    <?php if ($isInAlbum): ?>
                                                        · <?= (int) $experience['post_count'] ?> opslag
                                                    <?php endif; ?>
                                                </span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </section>

            </div>
        </div>
    </div>
</main>