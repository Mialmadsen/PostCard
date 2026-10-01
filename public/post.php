<?php
$pageTitle = 'Prækestolen, Norge';
$isLoggedIn = true;
$pageStyle = 'post';
$pageScripts = ['lightbox', 'confirm'];
require __DIR__ . '/../views/partials/header.php';
?>

<main id="main" class="site-main">
    <div class="container">
        <nav class="breadcrumb" aria-label="Du er her">
            <ol class="breadcrumb__list">
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="home.php">Mine grupper</a></li>
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="group.php?id=1">Familien Nielsen</a></li>
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="experience.php?id=1">Sommer i Norge</a></li>
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="post.php?id=6" aria-current="page">Prækestolen, Norge</a></li>
            </ol>
        </nav>

        <div class="post-layout">

            <!-- Left: the photos (post_images, ordered by sort_order) -->
            <div class="flow">
                <article class="polaroid post-polaroid">
                    <!-- LOOP: post_images — alt text comes from post_images.alt_text -->
                    <ul class="post-photos">
                        <li class="post-photos__item">
                            <a class="post-photos__link js-lightbox-link" href="assets/images/sample/photo-4.jpg">
                                <img class="polaroid__photo" src="assets/images/sample/photo-4.jpg" width="800" height="600"
                                    alt="Udsigt fra Prækestolen ned over Lysefjorden med stejle klipper og blåt vand.">
                                <span class="visually-hidden">(vis billedet stort)</span>
                            </a>
                        </li>
                        <li class="post-photos__item">
                            <a class="post-photos__link js-lightbox-link" href="assets/images/sample/photo-1.jpg">
                                <img class="polaroid__photo" src="assets/images/sample/photo-1.jpg" width="800" height="600" loading="lazy"
                                    alt="Granskov og en høj klippevæg ved en stille sø.">
                                <span class="visually-hidden">(vis billedet stort)</span>
                            </a>
                        </li>
                        <li class="post-photos__item">
                            <a class="post-photos__link js-lightbox-link" href="assets/images/sample/photo-3.jpg">
                                <img class="polaroid__photo" src="assets/images/sample/photo-3.jpg" width="800" height="600" loading="lazy"
                                    alt="Et vandfald der falder ned i en grøn kløft.">
                                <span class="visually-hidden">(vis billedet stort)</span>
                            </a>
                        </li>
                    </ul>
                    <!-- /LOOP -->

                    <div class="polaroid__body">
                        <h1 class="polaroid__title">Prækestolen, Norge</h1>
                    </div>
                </article>

                <!-- Previous / next post in the same experience (by posts.created_at) -->
                <nav class="post-pager" aria-label="Flere opslag i Sommer i Norge">
                    <a class="post-pager__link" href="post.php?id=5">← Forrige opslag</a>
                    <!-- IF a newer post exists: <a class="post-pager__link post-pager__link--next" href="post.php?id=…">Næste opslag →</a> -->
                </nav>
            </div>

            <!-- Right: text, likes and comments -->
            <div class="flow">
                <section class="card flow" aria-label="Opslaget">
                    <p class="group-label">Familien Nielsen · Sommer i Norge</p>
                    <p>Sidste dag i fjordene. Vi gik op til Prækestolen igen for at sige farvel til udsigten. Fire timer op og ned, ømme ben – og det var det hele værd. Tak for en fantastisk tur, alle sammen!</p>
                    <p class="meta">Mia Nielsen · <time datetime="2026-09-30T10:15">30. sep. 2026 kl. 10.15</time></p>

                    <ul class="post-stats">
                        <li>
                            <!-- A CSRF token is added in Epic 2 -->
                            <form action="like.php" method="post">
                                <input type="hidden" name="post_id" value="6">
                                <button class="post-stats__item like-button" type="submit" aria-pressed="false">
                                    <svg class="post-stats__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10Z" /></svg>
                                    4 <span class="visually-hidden">synes godt om</span>
                                </button>
                            </form>
                        </li>
                        <li class="post-stats__item">
                            <svg class="post-stats__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 5h16v11H9l-5 4V5Z" /></svg>
                            3 <span class="visually-hidden">kommentarer</span>
                        </li>
                        <li class="post-stats__item">Set af 6</li>
                    </ul>

                    <!-- IF the logged-in user owns this post (experiences.user_id = me). The server checks this again. -->
                    <div class="button-row">
                        <a class="button button--secondary" href="edit-post.php?id=6">Rediger opslag</a>
                        <!-- A CSRF token is added in Epic 2 -->
                        <form class="js-confirm" action="delete-post.php" method="post"
                            data-confirm="Vil du slette opslaget? Det kan ikke fortrydes.">
                            <input type="hidden" name="post_id" value="6">
                            <button class="button button--secondary" type="submit">Slet opslag</button>
                        </form>
                    </div>
                    <!-- /IF -->
                </section>

                <section class="card flow" id="comments" aria-labelledby="comments-heading">
                    <h2 class="section-title" id="comments-heading">Kommentarer</h2>

                    <!-- LOOP: comments on this post, oldest first -->
                    <ul class="comment-list">
                        <li class="comment-item">
                            <span class="avatar" aria-hidden="true">AN</span>
                            <div>
                                <p class="meta"><strong>Anna Nielsen</strong></p>
                                <p>Wow! Hvor lang tid tog det at gå derop?</p>
                                <p class="meta"><time datetime="2026-09-30T11:02">30. sep. kl. 11.02</time></p>
                            </div>
                        </li>
                        <li class="comment-item">
                            <span class="avatar" aria-hidden="true">MN</span>
                            <div>
                                <p class="meta"><strong>Mia Nielsen</strong></p>
                                <p>Ca. to timer hver vej. Vi tog det stille og roligt.</p>
                                <p class="meta"><time datetime="2026-09-30T11:20">30. sep. kl. 11.20</time></p>
                            </div>
                        </li>
                        <li class="comment-item">
                            <span class="avatar" aria-hidden="true">PN</span>
                            <div>
                                <p class="meta"><strong>Peter Nielsen</strong></p>
                                <p>Det skal vi prøve næste sommer!</p>
                                <p class="meta"><time datetime="2026-10-01T08:45">1. okt. kl. 08.45</time></p>
                            </div>
                        </li>
                    </ul>
                    <!-- /LOOP -->

                    <!-- A CSRF token is added in Epic 2 -->
                    <form class="comment-form" action="comment.php" method="post">
                        <input type="hidden" name="post_id" value="6">
                        <label class="field__label" for="comment-body">Skriv en kommentar</label>
                        <textarea class="field__input" id="comment-body" name="body" rows="3" maxlength="1000" required></textarea>
                        <button class="button button--primary" type="submit">Send kommentar</button>
                    </form>
                </section>
            </div>

        </div>
    </div>
</main>

<!-- The large photo view, opened by lightbox.js -->
<dialog class="lightbox js-lightbox" aria-label="Billedet i stor størrelse">
    <img class="lightbox__image js-lightbox-image" src="" alt="">
    <p class="lightbox__counter js-lightbox-counter" aria-live="polite"></p>
    <button class="lightbox__button lightbox__button--close js-lightbox-close" type="button" aria-label="Luk">×</button>
    <button class="lightbox__button lightbox__button--prev js-lightbox-prev" type="button" aria-label="Forrige billede">‹</button>
    <button class="lightbox__button lightbox__button--next js-lightbox-next" type="button" aria-label="Næste billede">›</button>
</dialog>

<?php require __DIR__ . '/../views/partials/footer.php'; ?>
