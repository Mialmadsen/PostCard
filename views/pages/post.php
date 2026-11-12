<?php
// Angiver om opslaget vises af en operator.
$operatorView = $operatorView ?? false;

// Breadcrumbs til almindelige brugere og operatorer.
$breadcrumbs = $operatorView
    ? [
        ['label' => 'Admin Posts', 'path' => '/admin/posts'],
        ['label' => $post['location'] ?: 'Opslag', 'path' => '/admin/posts/show?id=' . $post['id']]
    ]
    : [
        ['label' => 'Mine grupper', 'path' => '/home'],
        ['label' => $post['group_name'], 'path' => '/group?id=' . $post['group_id']],
        ['label' => $post['experience_name'], 'path' => '/experience?id=' . $post['experience_id']],
        ['label' => $post['location'] ?: 'Opslag', 'path' => '/post?id=' . $post['id']]
    ];
?>

<main id="main" class="site-main">
    <div class="container">
        <?php require __DIR__ . '/../partials/breadcrumbs.php'; ?>
        <div class="post-layout">
            <!-- Billeder -->
            <div class="flow">
                <article class="polaroid post-polaroid">
                    <ul class="post-photos js-lightbox-gallery">
                        <?php foreach ($images as $image): ?>
                            <?php
                            // Henter billedet gennem den beskyttede route.
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
                    <div class="polaroid__body">
                        <h1 class="polaroid__title"><?= e($post['location'] ?: 'Opslag') ?></h1>
                    </div>
                </article>
            </div>

            <!-- Tekst, likes og kommentarer -->
            <div class="flow">
                <section class="card flow" aria-label="Opslaget">
                    
                    <p class="group-label">
                        <!-- Gruppen åbnes med den adgang, der passer til visningen -->
                        <a href="<?= e(url(($operatorView ? '/admin/groups/show?id=' : '/group?id=') . (int) $post['group_id'])) ?>">
                            <?= e($post['group_name']) ?>
                        </a> ·
                        <?php if ($operatorView): ?>
                            <a href="<?= e(url('/admin/experiences/show?id=' . (int) $post['experience_id'])) ?>">
                                <?= e($post['experience_name']) ?>
                            </a>
                        <?php else: ?>
                            <a href="<?= e(url('/experience?id=' . (int) $post['experience_id'])) ?>">
                                <?= e($post['experience_name']) ?>
                            </a>
                        <?php endif; ?>
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
                            <?php if (!$operatorView): ?>
                                <!-- Almindelige brugere kan like opslaget -->
                                <form action="<?= e(url('/post-like')) ?>" method="post" data-like-form>
                                    <?= \PostCard\Core\Csrf::field() ?>
                                    <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">
                                    <button type="submit" class="like-button"
                                            aria-pressed="<?= $hasLiked ? 'true' : 'false' ?>"
                                            aria-label="<?= $hasLiked ? 'Fjern like' : 'Synes godt om' ?>">
                                        <svg class="post-stats__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                            <path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10Z" />
                                        </svg>
                                    </button>
                                </form>
                                <button type="button" class="like-count-button" data-like-users
                                        data-post-id="<?= (int) $post['id'] ?>"
                                        data-url="<?= e(url('/post-likes?id=' . $post['id'])) ?>"
                                        aria-label="Se hvem der har liket opslaget">
                                    <span data-like-count><?= (int) $post['likes'] ?></span> likes
                                </button>
                            <?php else: ?>
                                <!-- Operatoren kan kun se antallet af likes -->
                                <span><?= (int) $post['likes'] ?> likes</span>
                            <?php endif; ?>
                        </li>
                        <li class="post-stats__item">
                            <svg class="post-stats__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                <path d="M4 5h16v11H9l-5 4V5Z" />
                            </svg>
                            <?= (int) $post['comments'] ?>
                            <span class="visually-hidden">kommentarer</span>
                        </li>
                        <li class="post-stats__item">
                            Set af <?= (int) $post['views'] ?>
                        </li>
                    </ul>

                    <!-- Kun opslagets ejer kan redigere eller slette -->
                    <?php if (!$operatorView && (int) $post['author_id'] === \PostCard\Core\Auth::userId()): ?>
                        <div class="button-row">
                            <a class="button button--secondary"
                               href="<?= e(url('/edit-post?id=' . $post['id'])) ?>">
                                Rediger opslag
                            </a>
                            <form action="<?= e(url('/delete-post')) ?>" method="post"
                                  onsubmit="return confirm('Er du sikker på, at du vil slette opslaget? Alle billeder, kommentarer og likes bliver også slettet.');">
                                <?= \PostCard\Core\Csrf::field() ?>
                                <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">
                                <button class="button button--secondary" type="submit">Slet opslag</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </section>

                <!-- Kommentarer -->
                <section class="card flow" id="comments" aria-labelledby="comments-heading">
                    <h2 class="section-title" id="comments-heading">Kommentarer</h2>
                    <?php if (empty($comments)): ?>
                        <p class="meta">Der er ingen kommentarer endnu.</p>
                    <?php else: ?>
                        <div class="comments-list flow">
                            <?php foreach ($comments as $comment): ?>
                                <article class="comment">
                                    <p class="comment__author">
                                        <strong><?= e(trim($comment['first_name'] . ' ' . $comment['last_name'])) ?></strong>
                                    </p>
                                    <p class="comment__body"><?= nl2br(e($comment['body'])) ?></p>
                                    <div class="comment__footer">
                                        <p class="meta">
                                            <time datetime="<?= e($comment['created_at']) ?>">
                                                <?= e(date('d. M Y \k\l. H.i', strtotime($comment['created_at']))) ?>
                                            </time>
                                        </p>
                                        <?php if (!$operatorView && (int) $comment['user_id'] === \PostCard\Core\Auth::userId()): ?>
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
                  
                                        <?php if ($operatorView): ?>
                                            <!-- Operatoren kan slette kommentarer fra alle opslag -->
                                            <form
                                                class="js-confirm"
                                                action="<?= e(url('/admin/posts/comments/delete')) ?>"
                                                method="post"
                                                data-confirm="Er du sikker på, at du vil slette denne kommentar?"
                                                data-confirm-button="Ja, slet kommentar"
                                            >
                                                <?= \PostCard\Core\Csrf::field() ?>

                                                <input type="hidden" name="comment_id"
                                                    value="<?= (int) $comment['comment_id'] ?>">

                                                <input type="hidden" name="post_id"
                                                    value="<?= (int) $post['id'] ?>">

                                                <button class="comment__action comment__action--delete" type="submit">
                                                    Slet kommentar
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Operatoren kan ikke skrive kommentarer -->
                    <?php if (!$operatorView): ?>
                        <form class="form" action="<?= e(url('/post-comment')) ?>" method="post">
                            <?= \PostCard\Core\Csrf::field() ?>
                            <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">
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
                    <?php endif; ?>
                </section>
            </div>
        </div>
    </div>
</main>
