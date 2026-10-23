<?php
// Sidens indhold bliver vist gennem vores fælles View::render()
// Header og footer bliver derfor indlæst automatisk
?>

<main id="main" class="site-main">
    <div class="container">
        <p class="eyebrow">Min forside</p>
        <h1>Hej <?= e($firstName ?? 'der') ?></h1>
        <p>Her er det nyeste fra dine grupper. Kun medlemmer af en gruppe kan se dens opslag.</p>

        <div class="sidebar-layout">

            <!-- Left column: upload + my groups -->
            <div class="flow">

                <!-- Viser oplevelser, som den indloggede bruger har oprettet -->
                <section class="card flow" aria-labelledby="upload-heading">
                    <h2 id="upload-heading">Mine oplevelser</h2>

                    <?php if (empty($experiences)): ?>
                        <!-- Vises hvis brugeren ikke har oprettet nogen oplevelser -->
                        <p class="meta">Du har ikke oprettet nogen oplevelser endnu.</p>
                    <?php else: ?>
                        <ul class="experience-list">
                            <?php foreach ($experiences as $experience): ?>
                                <li class="experience-list__item flow">
                                    <p class="group-label"><?= e($experience['group_name']) ?></p>

                                    <?php if ($experience['experience_status'] === 'rejected'): ?>
                                        <h3 class="experience-list__name"><?= e($experience['name']) ?></h3>
                                        <p class="meta">Afvist af gruppens admin</p>

                                    <?php elseif ($experience['experience_status'] === 'pending'): ?>
                                        <h3 class="experience-list__name"><?= e($experience['name']) ?></h3>
                                        <p class="meta">Venter på godkendelse fra gruppens admin</p>

                                    <?php else: ?>
                                        <h3 class="experience-list__name">
                                            <a href="<?= url('/experience?id=' . (int) $experience['experience_id']) ?>">
                                                <?= e($experience['name']) ?>
                                            </a>
                                        </h3>

                                        <!-- Viser antal opslag og slutdato -->
                                        <p class="meta">
                                            <?php if ($experience['posting_mode'] === 'open'): ?>
                                                <?= (int) $experience['post_count'] ?> opslag
                                            <?php else: ?>
                                                <?= (int) $experience['post_count'] ?> af <?= (int) $experience['post_limit'] ?> opslag brugt
                                            <?php endif; ?>
                                            · slutter <?= date('d. M', strtotime($experience['end_date'])) ?>
                                        </p>

                                        <!-- Knappen vises kun i oplevelsens aktive periode -->
                                        <?php if (
                                            $experience['experience_status'] === 'approved' &&
                                            date('Y-m-d') >= $experience['start_date'] &&
                                            date('Y-m-d') <= $experience['end_date']
                                        ): ?>
                                            <p><a class="button button--primary" href="<?= url('/create-post?experience=' . (int) $experience['experience_id']) ?>">Nyt opslag</a></p>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <!-- Link til at oprette en ny oplevelse -->
                    <p><a href="<?= url('/create-experience') ?>">+ Start en ny oplevelse</a></p>
                    <p class="meta">En oplevelse har en periode og et antal opslag (normalt 20, op til 5 billeder pr. opslag). I nogle grupper skal gruppens admin godkende den først.</p>
                </section>


                
                
                <!-- Viser den indloggede brugers grupper -->
                <section class="card" aria-labelledby="groups-heading">
                    <h2 class="section-title" id="groups-heading">Mine grupper</h2>

                    <?php if (empty($groups)): ?>
                        <!-- Vises hvis brugeren ikke er medlem af nogen grupper -->
                        <p class="meta">Du er ikke medlem af nogen grupper endnu.</p>
                    <?php else: ?>
                        <!-- Gennemgår brugerens aktive grupper -->
                        <ul class="group-list">
                            <?php foreach ($groups as $group): ?>
                                <li>
                                    <a class="group-list__link" href="<?= url('/group?id=' . (int) $group['group_id']) ?>">
                                        <span class="group-list__name"><?= e($group['name']) ?></span>
                                        <span class="meta">
                                            <?= $group['posting_mode'] === 'open' ? 'Åben for opslag' : 'Opslag med tilladelse' ?>
                                        </span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <!-- Link til at oprette en ny gruppe -->
                    <p><a href="<?= url('/create-group') ?>">+ Opret en ny gruppe</a></p>
                </section>


            </div>

            <!-- Right column: posts + comments -->
            <div class="flow">

                
                <!-- Viser fastgjorte opslag fra brugerens grupper -->
                <section aria-labelledby="sticky-heading">
                    <h2 class="section-title" id="sticky-heading">Fastgjort</h2>

                    <?php if (empty($stickyPosts)): ?>
                        <p class="meta">Der er ingen fastgjorte opslag i dine grupper endnu.</p>
                    <?php else: ?>
                        <?php foreach ($stickyPosts as $post): ?>
                            <article class="polaroid post-card post-card--sticky">
                                <a href="<?= url('/post?id=' . (int) $post['id']) ?>" tabindex="-1" aria-hidden="true">
                                    <?php if (!empty($post['image'])): ?>
                                        <img class="polaroid__photo" src="<?= e($post['image']) ?>" alt="" width="800" height="600" loading="lazy">
                                    <?php endif; ?>
                                </a>
                                <div class="polaroid__body">
                                    <p class="postmark postmark--date polaroid__postmark post-card__postmark">
                                        <time datetime="<?= e(date('Y-m-d', strtotime($post['date']))) ?>">
                                            <?= date('d', strtotime($post['date'])) ?><br><?= date('M', strtotime($post['date'])) ?>
                                        </time>
                                    </p>
                                    <p class="group-label"><?= e($post['group_name']) ?> · Fastgjort</p>
                                    <h3 class="polaroid__title">
                                        <a class="polaroid__link" href="<?= url('/post?id=' . (int) $post['id']) ?>">
                                            <?= e($post['location'] ?? '') ?>
                                        </a>
                                    </h3>
                                    <p class="post-card__caption"><?= e($post['caption'] ?? '') ?></p>
                                    <p class="meta">
                                        <?= e(trim($post['first_name'] . ' ' . $post['last_name'])) ?> ·
                                        <time datetime="<?= e($post['date']) ?>"><?= date('d.m.Y', strtotime($post['date'])) ?></time>
                                    </p>
                                    <ul class="post-stats">
                                        <li>
                                            <button class="post-stats__item like-button" type="button" aria-pressed="false">
                                                <svg class="post-stats__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10Z" /></svg>
                                                <?= (int) $post['likes'] ?> <span class="visually-hidden">synes godt om</span>
                                            </button>
                                        </li>
                                        <li>
                                            <a class="post-stats__item" href="<?= url('/post?id=' . (int) $post['id'] . '#comments') ?>">
                                                <svg class="post-stats__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 5h16v11H9l-5 4V5Z" /></svg>
                                                <?= (int) $post['comments'] ?> <span class="visually-hidden">kommentarer</span>
                                            </a>
                                        </li>
                                        <li class="post-stats__item">Set af <?= (int) $post['views'] ?></li>
                                    </ul>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </section>


                
                <!-- Viser populære opslag fra brugerens grupper -->
                <section aria-labelledby="trending-heading">
                    <h2 class="section-title" id="trending-heading">Populært i dine grupper</h2>

                    <?php if (empty($popularPosts)): ?>
                        <p class="meta">Der er ingen populære opslag i dine grupper endnu.</p>
                    <?php else: ?>
                        <div class="post-grid">
                            <?php foreach ($popularPosts as $post): ?>
                                <article class="polaroid post-card">
                                    <a href="<?= url('/post?id=' . (int) $post['id']) ?>" tabindex="-1" aria-hidden="true">
                                        <?php if (!empty($post['image'])): ?>
                                            <img class="polaroid__photo" src="<?= e($post['image']) ?>" alt="" width="800" height="600" loading="lazy">
                                        <?php endif; ?>
                                    </a>
                                    <div class="polaroid__body">
                                        <p class="postmark postmark--date polaroid__postmark post-card__postmark">
                                            <time datetime="<?= e(date('Y-m-d', strtotime($post['date']))) ?>">
                                                <?= date('d', strtotime($post['date'])) ?><br><?= date('M', strtotime($post['date'])) ?>
                                            </time>
                                        </p>
                                        <p class="group-label"><?= e($post['group_name']) ?></p>
                                        <h3 class="polaroid__title">
                                            <a class="polaroid__link" href="<?= url('/post?id=' . (int) $post['id']) ?>">
                                                <?= e($post['location']) ?>
                                            </a>
                                        </h3>
                                        <p class="post-card__caption"><?= e($post['caption']) ?></p>
                                        <p class="meta">
                                            <?= e(trim($post['first_name'] . ' ' . $post['last_name'])) ?> ·
                                            <time datetime="<?= e($post['date']) ?>"><?= date('d.m.Y', strtotime($post['date'])) ?></time>
                                        </p>
                                        <ul class="post-stats">
                                            <li>
                                                <button class="post-stats__item like-button" type="button" aria-pressed="false">
                                                    <svg class="post-stats__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10Z" /></svg>
                                                    <?= (int) $post['likes'] ?> <span class="visually-hidden">synes godt om</span>
                                                </button>
                                            </li>
                                            <li>
                                                <a class="post-stats__item" href="<?= url('/post?id=' . (int) $post['id'] . '#comments') ?>">
                                                    <svg class="post-stats__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 5h16v11H9l-5 4V5Z" /></svg>
                                                    <?= (int) $post['comments'] ?> <span class="visually-hidden">kommentarer</span>
                                                </a>
                                            </li>
                                            <li class="post-stats__item">Set af <?= (int) $post['views'] ?></li>
                                        </ul>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>


                
                <!-- Viser de nyeste kommentarer fra brugerens grupper -->
                <section class="card" aria-labelledby="comments-heading">
                    <h2 class="section-title" id="comments-heading">Nyeste kommentarer</h2>

                    <?php if (empty($latestComments)): ?>
                        <p class="meta">Der er ingen kommentarer i dine grupper endnu.</p>
                    <?php else: ?>
                        <ul class="comment-list">
                            <?php foreach ($latestComments as $comment): ?>
                                <?php
                                    // Henter forfatterens fulde navn og initialer
                                    $fullName = trim($comment['first_name'] . ' ' . $comment['last_name']);
                                    $initials = mb_strtoupper(
                                        mb_substr($comment['first_name'], 0, 1) .
                                        mb_substr($comment['last_name'], 0, 1)
                                    );
                                ?>
                                <li class="comment-item">
                                    <span class="avatar" aria-hidden="true"><?= e($initials) ?></span>
                                    <div>
                                        <p class="meta">
                                            <strong><?= e($fullName) ?></strong> på
                                            <a href="<?= url('/post?id=' . (int) $comment['post_id']) ?>#comments">
                                                <?= e($comment['location'] ?? 'Opslag') ?>
                                            </a>
                                            · <?= e($comment['group_name']) ?>
                                        </p>
                                        <p><?= e($comment['body']) ?></p>
                                        <p class="meta">
                                            <time datetime="<?= e($comment['created_at']) ?>">
                                                <?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?>
                                            </time>
                                        </p>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </section>


            </div>
        </div>
    </div>
</main>
