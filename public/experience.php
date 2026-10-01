<?php
$pageTitle = 'Sommer i Norge';
$isLoggedIn = true;
$pageStyle = 'experience';
require __DIR__ . '/../views/partials/header.php';
?>

<main id="main" class="site-main">
    <div class="container">
        <nav class="breadcrumb" aria-label="Du er her">
            <ol class="breadcrumb__list">
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="home.php">Mine grupper</a></li>
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="group.php?id=1">Familien Nielsen</a></li>
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="experience.php?id=1" aria-current="page">Sommer i Norge</a></li>
            </ol>
        </nav>

        <header class="experience-header">
            <p class="group-label">Familien Nielsen</p>
            <h1 class="experience-header__title">Sommer i Norge</h1>
            <p class="meta">Mia Nielsen · <time datetime="2026-09-10">10. sep.</time> – <time datetime="2026-10-15">15. okt. 2026</time> · I gang</p>
            <p class="experience-header__text">Fem uger i bil gennem Norge – fra fjordene i syd til kysten ved Ålesund. Vi deler de bedste øjeblikke her.</p>
        </header>

        <!-- IF the logged-in user owns this experience AND it is active -->
        <section class="card experience-owner" aria-labelledby="owner-heading">
            <h2 id="owner-heading">Din oplevelse</h2>
            <label class="meta" for="post-progress">6 af 20 opslag brugt · 14 tilbage · slutter om 14 dage</label>
            <progress class="experience-owner__progress" id="post-progress" value="6" max="20">6 af 20</progress>
            <p><a class="button button--primary" href="create-post.php?experience=1">Nyt opslag</a></p>
        </section>
        <!-- /IF -->

        <section class="experience-posts" aria-labelledby="posts-heading">
            <h2 class="section-title" id="posts-heading">Opslag <span class="meta">(6)</span></h2>

            <!-- LOOP: posts in this experience, newest first — each card becomes views/partials/post-card.php
                 Heading = posts.location, text = posts.caption, date = posts.created_at -->
            <div class="post-grid">
                    <article class="polaroid post-card">
                        <a href="post.php?id=6" tabindex="-1" aria-hidden="true">
                            <img class="polaroid__photo" src="assets/images/sample/photo-4.jpg"
                                alt="" width="800" height="600" loading="lazy">
                        </a>
                        <div class="polaroid__body">
                            <p class="postmark postmark--date polaroid__postmark post-card__postmark"><time datetime="2026-09-30">30<br>sep</time></p>
                            <h3 class="polaroid__title">
                                <a class="polaroid__link" href="post.php?id=6">Prækestolen, Norge</a>
                            </h3>
                            <p class="post-card__caption">Sidste dag i fjordene. Vi gik op til Prækestolen igen for at sige farvel til udsigten.</p>
                            <p class="meta"><time datetime="2026-09-30">for 1 dag siden</time></p>
                            <ul class="post-stats">
                                <li>
                                    <button class="post-stats__item like-button" type="button" aria-pressed="false">
                                        <svg class="post-stats__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10Z" /></svg>
                                        4 <span class="visually-hidden">synes godt om</span>
                                    </button>
                                </li>
                                <li>
                                    <a class="post-stats__item" href="post.php?id=6#comments">
                                        <svg class="post-stats__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 5h16v11H9l-5 4V5Z" /></svg>
                                        1 <span class="visually-hidden">kommentarer</span>
                                    </a>
                                </li>
                                <li class="post-stats__item">Set af 6</li>
                            </ul>
                        </div>
                    </article>
                    <article class="polaroid post-card">
                        <a href="post.php?id=5" tabindex="-1" aria-hidden="true">
                            <img class="polaroid__photo" src="assets/images/sample/photo-2.jpg"
                                alt="" width="800" height="600" loading="lazy">
                        </a>
                        <div class="polaroid__body">
                            <p class="postmark postmark--date polaroid__postmark post-card__postmark"><time datetime="2026-09-26">26<br>sep</time></p>
                            <h3 class="polaroid__title">
                                <a class="polaroid__link" href="post.php?id=5">Jotunheimen, Norge</a>
                            </h3>
                            <p class="post-card__caption">Vi vågnede til sne i teltet! Kold nat, men den smukkeste morgen.</p>
                            <p class="meta"><time datetime="2026-09-26">for 5 dage siden</time></p>
                            <ul class="post-stats">
                                <li>
                                    <button class="post-stats__item like-button" type="button" aria-pressed="false">
                                        <svg class="post-stats__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10Z" /></svg>
                                        9 <span class="visually-hidden">synes godt om</span>
                                    </button>
                                </li>
                                <li>
                                    <a class="post-stats__item" href="post.php?id=5#comments">
                                        <svg class="post-stats__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 5h16v11H9l-5 4V5Z" /></svg>
                                        4 <span class="visually-hidden">kommentarer</span>
                                    </a>
                                </li>
                                <li class="post-stats__item">Set af 10</li>
                            </ul>
                        </div>
                    </article>
                    <article class="polaroid post-card">
                        <a href="post.php?id=4" tabindex="-1" aria-hidden="true">
                            <img class="polaroid__photo" src="assets/images/sample/photo-3.jpg"
                                alt="" width="800" height="600" loading="lazy">
                        </a>
                        <div class="polaroid__body">
                            <p class="postmark postmark--date polaroid__postmark post-card__postmark"><time datetime="2026-09-21">21<br>sep</time></p>
                            <h3 class="polaroid__title">
                                <a class="polaroid__link" href="post.php?id=4">Hardanger, Norge</a>
                            </h3>
                            <p class="post-card__caption">Vi kunne høre det længe før vi kunne se det.</p>
                            <p class="meta"><time datetime="2026-09-21">for 10 dage siden</time></p>
                            <ul class="post-stats">
                                <li>
                                    <button class="post-stats__item like-button" type="button" aria-pressed="false">
                                        <svg class="post-stats__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10Z" /></svg>
                                        7 <span class="visually-hidden">synes godt om</span>
                                    </button>
                                </li>
                                <li>
                                    <a class="post-stats__item" href="post.php?id=4#comments">
                                        <svg class="post-stats__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 5h16v11H9l-5 4V5Z" /></svg>
                                        2 <span class="visually-hidden">kommentarer</span>
                                    </a>
                                </li>
                                <li class="post-stats__item">Set af 11</li>
                            </ul>
                        </div>
                    </article>
                    <article class="polaroid post-card">
                        <a href="post.php?id=3" tabindex="-1" aria-hidden="true">
                            <img class="polaroid__photo" src="assets/images/sample/photo-1.jpg"
                                alt="" width="800" height="600" loading="lazy">
                        </a>
                        <div class="polaroid__body">
                            <p class="postmark postmark--date polaroid__postmark post-card__postmark"><time datetime="2026-09-17">17<br>sep</time></p>
                            <h3 class="polaroid__title">
                                <a class="polaroid__link" href="post.php?id=3">Gjende, Norge</a>
                            </h3>
                            <p class="post-card__caption">Madpakker, kaffe og alt for kolde tæer i vandet.</p>
                            <p class="meta"><time datetime="2026-09-17">for 14 dage siden</time></p>
                            <ul class="post-stats">
                                <li>
                                    <button class="post-stats__item like-button" type="button" aria-pressed="false">
                                        <svg class="post-stats__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10Z" /></svg>
                                        5 <span class="visually-hidden">synes godt om</span>
                                    </button>
                                </li>
                                <li>
                                    <a class="post-stats__item" href="post.php?id=3#comments">
                                        <svg class="post-stats__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 5h16v11H9l-5 4V5Z" /></svg>
                                        3 <span class="visually-hidden">kommentarer</span>
                                    </a>
                                </li>
                                <li class="post-stats__item">Set af 12</li>
                            </ul>
                        </div>
                    </article>
                    <article class="polaroid post-card">
                        <a href="post.php?id=2" tabindex="-1" aria-hidden="true">
                            <img class="polaroid__photo" src="assets/images/sample/photo-6.jpg"
                                alt="" width="800" height="600" loading="lazy">
                        </a>
                        <div class="polaroid__body">
                            <p class="postmark postmark--date polaroid__postmark post-card__postmark"><time datetime="2026-09-14">14<br>sep</time></p>
                            <h3 class="polaroid__title">
                                <a class="polaroid__link" href="post.php?id=2">Ålesund, Norge</a>
                            </h3>
                            <p class="post-card__caption">Blæst, bølger og måger, der ville have vores fiskefrikadeller.</p>
                            <p class="meta"><time datetime="2026-09-14">for 17 dage siden</time></p>
                            <ul class="post-stats">
                                <li>
                                    <button class="post-stats__item like-button" type="button" aria-pressed="false">
                                        <svg class="post-stats__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10Z" /></svg>
                                        6 <span class="visually-hidden">synes godt om</span>
                                    </button>
                                </li>
                                <li>
                                    <a class="post-stats__item" href="post.php?id=2#comments">
                                        <svg class="post-stats__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 5h16v11H9l-5 4V5Z" /></svg>
                                        0 <span class="visually-hidden">kommentarer</span>
                                    </a>
                                </li>
                                <li class="post-stats__item">Set af 12</li>
                            </ul>
                        </div>
                    </article>
                    <article class="polaroid post-card">
                        <a href="post.php?id=1" tabindex="-1" aria-hidden="true">
                            <img class="polaroid__photo" src="assets/images/sample/photo-5.jpg"
                                alt="" width="800" height="600" loading="lazy">
                        </a>
                        <div class="polaroid__body">
                            <p class="postmark postmark--date polaroid__postmark post-card__postmark"><time datetime="2026-09-10">10<br>sep</time></p>
                            <h3 class="polaroid__title">
                                <a class="polaroid__link" href="post.php?id=1">Ryfylke, Norge</a>
                            </h3>
                            <p class="post-card__caption">Vi er på vej! Første stop på vejen nordpå – lad sommerferien begynde.</p>
                            <p class="meta"><time datetime="2026-09-10">for 21 dage siden</time></p>
                            <ul class="post-stats">
                                <li>
                                    <button class="post-stats__item like-button" type="button" aria-pressed="false">
                                        <svg class="post-stats__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10Z" /></svg>
                                        11 <span class="visually-hidden">synes godt om</span>
                                    </button>
                                </li>
                                <li>
                                    <a class="post-stats__item" href="post.php?id=1#comments">
                                        <svg class="post-stats__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 5h16v11H9l-5 4V5Z" /></svg>
                                        5 <span class="visually-hidden">kommentarer</span>
                                    </a>
                                </li>
                                <li class="post-stats__item">Set af 13</li>
                            </ul>
                        </div>
                    </article>
            </div>
            <!-- /LOOP -->
        </section>
    </div>
</main>

<?php require __DIR__ . '/../views/partials/footer.php'; ?>
