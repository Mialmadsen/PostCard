<?php
// Breadcrumbs viser, hvor brugeren befinder sig på hjemmesiden.
$breadcrumbs = [
    ['label' => 'Mine grupper', 'path' => '/home'],
    [
        'label' => $post['group_name'],
        'path' => '/group?id=' . $post['group_id']
    ],
    [
        'label' => $post['experience_name'],
        'path' => '/experience?id=' . $post['experience_id']
    ],
    [
        'label' => $post['location'] ?: 'Opslag',
        'path' => '/post?id=' . $post['id']
    ]
];
?>

<main id="main" class="site-main">
    <div class="container">
        <?php require __DIR__ . '/../partials/breadcrumbs.php'; ?>

        <div class="post-layout">
            <?php /* Left: the photos (post_images, ordered by sort_order) */ ?>
            <div class="flow">
                <article class="polaroid post-polaroid">
                    <?php /* LOOP: post_images — alt text comes from post_images.alt_text */ ?>
                    <ul class="post-photos js-lightbox-gallery">
                        <?php foreach ($images as $image): ?>
                            <?php
                            // Henter billedet gennem den beskyttede post-image-route.
                            $imageUrl = url('/post-image?id=' . (int) $image['image_id']);
                            ?>
                            <li class="post-photos__item">
                                <a class="post-photos__link js-lightbox-link" href="<?= e($imageUrl) ?>">
                                    <img class="polaroid__photo" src="<?= e($imageUrl) ?>"
                                         alt="<?= e($image['alt_text'] ?? '') ?>" loading="lazy">
                                    <span class="visually-hidden">(vis billedet stort)</span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php /* /LOOP */ ?>

                    <div class="polaroid__body">
                        <h1 class="polaroid__title"><?= e($post['location'] ?: 'Opslag') ?></h1>
                    </div>
                </article>
                <?php /* Previous / next post in the same experience (kommer senere) */ ?>
            </div>

            <?php /* Right: text, likes and comments */ ?>
            <div class="flow">
                <section class="card flow" aria-label="Opslaget">
                    <p class="group-label">
                        <?= e($post['group_name']) ?> · <?= e($post['experience_name']) ?>
                    </p>

                    <p><?= nl2br(e($post['caption'] ?? '')) ?></p>

                    <p class="meta">
                        <?= e(trim($post['first_name'] . ' ' . $post['last_name'])) ?> ·
                        <time datetime="<?= e($post['date']) ?>">
                            <?= e(date('d. M Y \k\l. H.i', strtotime($post['date']))) ?>
                        </time>
                    </p>

                    <ul class="post-stats">
                        <li class="post-stats__item">
                            <?php /* Formular til at like eller unlike opslaget. */ ?>
                            <form action="<?= e(url('/post-like')) ?>" method="post" data-like-form>
                                <?php /* Beskytter formularen mod CSRF. */ ?>
                                <?= \PostCard\Core\Csrf::field() ?>
                                <?php /* Sender opslagets ID med formularen. */ ?>
                                <input type="hidden" name="post_id" value="<?= e((string) $post['id']) ?>">
                                <?php /* Hjertet fungerer som en like-knap. */ ?>
                                <button type="submit" class="like-button"
                                        aria-pressed="<?= $hasLiked ? 'true' : 'false' ?>"
                                        aria-label="<?= $hasLiked ? 'Fjern like' : 'Synes godt om' ?>">
                                    <svg class="post-stats__icon" viewBox="0 0 24 24"
                                         aria-hidden="true" focusable="false">
                                        <path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10Z" />
                                    </svg>
                                    <span data-like-count><?= (int) $post['likes'] ?></span>
                                </button>
                            </form>
                        </li>

                        <li class="post-stats__item">
                            <svg class="post-stats__icon" viewBox="0 0 24 24"
                                 aria-hidden="true" focusable="false">
                                <path d="M4 5h16v11H9l-5 4V5Z" />
                            </svg>
                            <?= (int) $post['comments'] ?>
                            <span class="visually-hidden">kommentarer</span>
                        </li>

                        <li class="post-stats__item">
                            Set af <?= (int) $post['views'] ?>
                        </li>
                    </ul>

                    
                    <?php /* SLET OG REDIGER OPSLAG Kun opslagets ejer kan se knapperne til redigering og sletning. */ ?>
                    <?php if ((int) $post['author_id'] === \PostCard\Core\Auth::userId()): ?>
                        <div class="button-row">

                            <?php /* Sender brugeren til redigeringssiden. */ ?>
                            <a class="button button--secondary"
                            href="<?= e(url('/edit-post?id=' . $post['id'])) ?>">
                                Rediger opslag
                            </a>

                            <?php /* Sender en POST-anmodning for at slette opslaget. */ ?>
                            <form action="<?= e(url('/delete-post')) ?>" method="post"
                                onsubmit="return confirm('Er du sikker på, at du vil slette opslaget? Alle billeder, kommentarer og likes bliver også slettet.');">
                                <?= \PostCard\Core\Csrf::field() ?>
                                <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">
                                <button class="button button--secondary" type="submit">
                                    Slet opslag
                                </button>
                            </form>
                            
                        </div>
                    <?php endif; ?>

                </section>

                <?php /* Kommentarer til opslaget */ ?>
                <section class="card flow" id="comments" aria-labelledby="comments-heading">
                    <h2 class="section-title" id="comments-heading">Kommentarer</h2>

                    <?php /* Viser en besked, hvis opslaget ikke har nogen kommentarer. */ ?>
                    <?php if (empty($comments)): ?>
                        <p class="meta">Der er ingen kommentarer endnu.</p>
                    <?php else: ?>
                        <?php /* Gennemgår alle kommentarer til opslaget. */ ?>
                        <div class="comments-list flow">
                            
                        <?php /* LOOP: comments — viser hver kommentar med forfatter, tekst og tidspunkt */ ?>
                        <?php foreach ($comments as $comment): ?>
                            <article class="comment">
                                <?php /* Viser navnet på brugeren, der har skrevet kommentaren. */ ?>
                                <p class="comment__author">
                                    <strong><?= e(trim($comment['first_name'] . ' ' . $comment['last_name'])) ?></strong>
                                </p>

                                <?php /* Viser kommentarens tekst. */ ?>
                                <p class="comment__body"><?= nl2br(e($comment['body'])) ?></p>

                                
                                <?php /* Dato og handlinger vises på samme linje. */ ?>
                                <div class="comment__footer">
                                    <p class="meta">
                                        <time datetime="<?= e($comment['created_at']) ?>">
                                            <?= e(date('d. M Y \k\l. H.i', strtotime($comment['created_at']))) ?>
                                        </time>
                                    </p>

                                    <?php if ((int) $comment['user_id'] === \PostCard\Core\Auth::userId()): ?>
                                        <details class="comment__edit">
                                            <summary class="comment__action">Rediger</summary>

                                            <form class="form" action="<?= e(url('/update-comment')) ?>" method="post">
                                                <?= \PostCard\Core\Csrf::field() ?>
                                                <input type="hidden" name="comment_id" value="<?= (int) $comment['comment_id'] ?>">

                                                <div class="field">
                                                    <label class="field__label" for="edit-comment-<?= (int) $comment['comment_id'] ?>">
                                                        Rediger kommentar
                                                    </label>
                                                    <textarea class="field__input"
                                                            id="edit-comment-<?= (int) $comment['comment_id'] ?>"
                                                            name="body" rows="3" maxlength="2000" required><?= e($comment['body']) ?></textarea>
                                                </div>

                                                <button class="button button--primary" type="submit">Gem ændringer</button>
                                            </form>
                                        </details>

                                        <form action="<?= e(url('/delete-comment')) ?>" method="post"
                                            onsubmit="return confirm('Er du sikker på, at du vil slette kommentaren?');">
                                            <?= \PostCard\Core\Csrf::field() ?>
                                            <input type="hidden" name="comment_id" value="<?= (int) $comment['comment_id'] ?>">
                                            <button class="comment__action comment__action--delete" type="submit">Slet</button>
                                        </form>
                                    <?php endif; ?>
                                </div>

                            </article>
                        <?php endforeach; ?>

                        </div>
                    <?php endif; ?>

                    <?php /* Formular til at skrive en ny kommentar. */ ?>
                    <form class="form" action="<?= e(url('/post-comment')) ?>" method="post">
                        <?php /* Beskytter formularen mod CSRF. */ ?>
                        <?= \PostCard\Core\Csrf::field() ?>
                        <?php /* Sender opslagets ID med formularen. */ ?>
                        <input type="hidden" name="post_id" value="<?= e((string) $post['id']) ?>">

                        <div class="field">
                            <label class="field__label" for="comment-body">Skriv en kommentar</label>
                            <textarea class="field__input" id="comment-body" name="body" rows="3"
                                      maxlength="2000" required aria-describedby="comment-hint"></textarea>
                            <p class="meta" id="comment-hint">Del dine tanker om opslaget med gruppen.</p>
                        </div>

                        <div class="button-row">
                            <button class="button button--primary" type="submit">Send kommentar</button>
                        </div>
                    </form>
                </section>
            </div>
        </div>
    </div>
</main>
