<?php
$pageTitle = 'Min forside';
$isLoggedIn = true;
$pageStyle = 'home';
require __DIR__ . '/../views/partials/header.php';
?>

<main id="main" class="site-main">
    <div class="container">
        <p class="eyebrow">Min forside</p>
        <h1>Hej Mia</h1>
        <p>Her er det nyeste fra dine grupper. Kun medlemmer af en gruppe kan se dens opslag.</p>

        <div class="home__layout">

            <!-- Left column: upload + my groups -->
            <div class="flow">
                <section class="card flow" aria-labelledby="upload-heading">
                    <h2 id="upload-heading">Mine oplevelser</h2>

                    <!-- LOOP: my experiences (experiences.user_id = me, approved or pending) -->
                    <ul class="experience-list">
                        <li class="experience-list__item flow">
                            <p class="group-label">Familien Nielsen</p>
                            <h3 class="experience-list__name">Sommer i Norge</h3>
                            <p class="meta">8 af 20 opslag brugt · slutter 15. okt.</p>
                            <p><a class="button button--primary" href="create-post.php?experience=1">Nyt opslag</a></p>
                        </li>
                        <li class="experience-list__item flow">
                            <p class="group-label">Klasse 3.B</p>
                            <h3 class="experience-list__name">Studietur til Berlin</h3>
                            <p class="meta">Venter på godkendelse fra gruppens admin</p>
                        </li>
                    </ul>
                    <!-- /LOOP -->

                    <p><a href="create-experience.php">+ Start en ny oplevelse</a></p>
                    <p class="meta">En oplevelse har en periode og et antal opslag (normalt 20, op til 5 billeder pr. opslag). I nogle grupper skal gruppens admin godkende den først.</p>
                </section>

                <section class="card" aria-labelledby="groups-heading">
                    <h2 class="home__section-title" id="groups-heading">Mine grupper</h2>

                    <!-- LOOP: my groups (v_user_groups) -->
                    <ul class="group-list">
                        <li>
                            <a class="group-list__link" href="group.php?id=1">
                                <span class="group-list__name">Familien Nielsen</span>
                                <span class="meta">13 medlemmer · Åben for opslag</span>
                            </a>
                        </li>
                        <li>
                            <a class="group-list__link" href="group.php?id=2">
                                <span class="group-list__name">Klasse 3.B</span>
                                <span class="meta">24 medlemmer · Opslag med tilladelse</span>
                            </a>
                        </li>
                        <li>
                            <a class="group-list__link" href="group.php?id=3">
                                <span class="group-list__name">Løbeklubben</span>
                                <span class="meta">9 medlemmer · Åben for opslag</span>
                            </a>
                        </li>
                    </ul>
                    <!-- /LOOP -->

                    <p><a href="create-group.php">+ Opret en ny gruppe</a></p>
                </section>
            </div>

            <!-- Right column: posts + comments -->
            <div class="flow">

                <section aria-labelledby="sticky-heading">
                    <h2 class="home__section-title" id="sticky-heading">Fastgjort</h2>

                    <!-- LOOP: sticky posts (posts.is_sticky = 1) — each card becomes views/partials/post-card.php -->
                    <article class="post-card post-card--sticky">
                        <a href="post.php?id=1" tabindex="-1" aria-hidden="true">
                            <img class="post-card__photo" src="assets/images/sample/photo-4.jpg"
                                alt="" width="800" height="600" loading="lazy">
                        </a>
                        <div class="post-card__body">
                            <p class="postmark postmark--date post-card__postmark"><time datetime="2026-09-12">12<br>sep</time></p>
                            <p class="group-label">Familien Nielsen · Fastgjort</p>
                            <h3 class="post-card__title">
                                <a class="post-card__title-link" href="post.php?id=1">Lysefjorden, Norge</a>
                            </h3>
                            <p class="post-card__caption">Vi gik op til Prækestolen i morges. Udsigten over fjorden var det hele værd!</p>
                            <p class="meta">Mia Nielsen · <time datetime="2026-09-12T09:30">for 3 dage siden</time></p>
                            <ul class="post-stats">
                                <li>
                                    <button class="post-stats__item like-button" type="button" aria-pressed="true">
                                        <svg class="post-stats__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10Z" /></svg>
                                        12 <span class="visually-hidden">synes godt om</span>
                                    </button>
                                </li>
                                <li>
                                    <a class="post-stats__item" href="post.php?id=1#comments">
                                        <svg class="post-stats__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 5h16v11H9l-5 4V5Z" /></svg>
                                        5 <span class="visually-hidden">kommentarer</span>
                                    </a>
                                </li>
                                <li class="post-stats__item">Set af 11</li>
                            </ul>
                        </div>
                    </article>
                    <!-- /LOOP -->
                </section>

                <section aria-labelledby="trending-heading">
                    <h2 class="home__section-title" id="trending-heading">Populært i dine grupper</h2>

                    <!-- LOOP: trending posts (most likes in the last 7 days, only my groups) -->
                    <div class="post-grid">
                        <article class="post-card">
                            <a href="post.php?id=2" tabindex="-1" aria-hidden="true">
                                <img class="post-card__photo" src="assets/images/sample/photo-5.jpg"
                                    alt="" width="800" height="600" loading="lazy">
                            </a>
                            <div class="post-card__body">
                                <p class="postmark postmark--date post-card__postmark"><time datetime="2026-09-28">28<br>sep</time></p>
                                <p class="group-label">Familien Nielsen</p>
                                <h3 class="post-card__title">
                                    <a class="post-card__title-link" href="post.php?id=2">Isle of Skye, Skotland</a>
                                </h3>
                                <p class="post-card__caption">Den smalleste vej, jeg nogensinde har kørt på – men sikke en udsigt.</p>
                                <p class="meta">Anna Nielsen · <time datetime="2026-09-28T16:10">i går</time></p>
                                <ul class="post-stats">
                                    <li>
                                        <button class="post-stats__item like-button" type="button" aria-pressed="false">
                                            <svg class="post-stats__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10Z" /></svg>
                                            9 <span class="visually-hidden">synes godt om</span>
                                        </button>
                                    </li>
                                    <li>
                                        <a class="post-stats__item" href="post.php?id=2#comments">
                                            <svg class="post-stats__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 5h16v11H9l-5 4V5Z" /></svg>
                                            3 <span class="visually-hidden">kommentarer</span>
                                        </a>
                                    </li>
                                    <li class="post-stats__item">Set af 10</li>
                                </ul>
                            </div>
                        </article>

                        <article class="post-card">
                            <a href="post.php?id=3" tabindex="-1" aria-hidden="true">
                                <img class="post-card__photo" src="assets/images/sample/photo-3.jpg"
                                    alt="" width="800" height="600" loading="lazy">
                            </a>
                            <div class="post-card__body">
                                <p class="postmark postmark--date post-card__postmark"><time datetime="2026-09-25">25<br>sep</time></p>
                                <p class="group-label">Løbeklubben</p>
                                <h3 class="post-card__title">
                                    <a class="post-card__title-link" href="post.php?id=3">Trailløb ved vandfaldet</a>
                                </h3>
                                <p class="post-card__caption">14 km gennem skoven og et velfortjent hvil ved vandfaldet.</p>
                                <p class="meta">Jonas Berg · <time datetime="2026-09-25T11:00">for 6 dage siden</time></p>
                                <ul class="post-stats">
                                    <li>
                                        <button class="post-stats__item like-button" type="button" aria-pressed="false">
                                            <svg class="post-stats__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10Z" /></svg>
                                            7 <span class="visually-hidden">synes godt om</span>
                                        </button>
                                    </li>
                                    <li>
                                        <a class="post-stats__item" href="post.php?id=3#comments">
                                            <svg class="post-stats__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 5h16v11H9l-5 4V5Z" /></svg>
                                            2 <span class="visually-hidden">kommentarer</span>
                                        </a>
                                    </li>
                                    <li class="post-stats__item">Set af 8</li>
                                </ul>
                            </div>
                        </article>

                        <article class="post-card">
                            <a href="post.php?id=4" tabindex="-1" aria-hidden="true">
                                <img class="post-card__photo" src="assets/images/sample/photo-6.jpg"
                                    alt="" width="800" height="600" loading="lazy">
                            </a>
                            <div class="post-card__body">
                                <p class="postmark postmark--date post-card__postmark"><time datetime="2026-09-24">24<br>sep</time></p>
                                <p class="group-label">Klasse 3.B</p>
                                <h3 class="post-card__title">
                                    <a class="post-card__title-link" href="post.php?id=4">Kystklipperne, Californien</a>
                                </h3>
                                <p class="post-card__caption">Studieturens sidste dag. Vi så sæler nede ved klipperne!</p>
                                <p class="meta">Sara Holm · <time datetime="2026-09-24T18:45">for 7 dage siden</time></p>
                                <ul class="post-stats">
                                    <li>
                                        <button class="post-stats__item like-button" type="button" aria-pressed="false">
                                            <svg class="post-stats__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10Z" /></svg>
                                            6 <span class="visually-hidden">synes godt om</span>
                                        </button>
                                    </li>
                                    <li>
                                        <a class="post-stats__item" href="post.php?id=4#comments">
                                            <svg class="post-stats__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 5h16v11H9l-5 4V5Z" /></svg>
                                            4 <span class="visually-hidden">kommentarer</span>
                                        </a>
                                    </li>
                                    <li class="post-stats__item">Set af 19</li>
                                </ul>
                            </div>
                        </article>
                    </div>
                    <!-- /LOOP -->
                </section>

                <section class="card" aria-labelledby="comments-heading">
                    <h2 class="home__section-title" id="comments-heading">Nyeste kommentarer</h2>

                    <!-- LOOP: latest comments in my groups -->
                    <ul class="comment-list">
                        <li class="comment-item">
                            <span class="avatar" aria-hidden="true">AN</span>
                            <div>
                                <p class="meta"><strong>Anna Nielsen</strong> på <a href="post.php?id=1#comments">Lysefjorden, Norge</a> · Familien Nielsen</p>
                                <p>Hvor er det flot! Hvor lang tid tog turen derop?</p>
                                <p class="meta"><time datetime="2026-10-01T09:12">for 2 timer siden</time></p>
                            </div>
                        </li>
                        <li class="comment-item">
                            <span class="avatar" aria-hidden="true">JB</span>
                            <div>
                                <p class="meta"><strong>Jonas Berg</strong> på <a href="post.php?id=3#comments">Trailløb ved vandfaldet</a> · Løbeklubben</p>
                                <p>Jeg er med næste gang – også selvom det regner.</p>
                                <p class="meta"><time datetime="2026-09-30T20:40">i går</time></p>
                            </div>
                        </li>
                        <li class="comment-item">
                            <span class="avatar" aria-hidden="true">SH</span>
                            <div>
                                <p class="meta"><strong>Sara Holm</strong> på <a href="post.php?id=4#comments">Kystklipperne, Californien</a> · Klasse 3.B</p>
                                <p>Tak for en fantastisk tur, alle sammen.</p>
                                <p class="meta"><time datetime="2026-09-29T15:05">for 2 dage siden</time></p>
                            </div>
                        </li>
                    </ul>
                    <!-- /LOOP -->
                </section>

            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../views/partials/footer.php'; ?>
