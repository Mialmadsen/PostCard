<?php
/* Filen bruges til at vise hvert opslag i vores experience.
   Der bruges en foreach-loop, som indlæser partial for hvert opslag.
   Infoen kommer fra databasen gennem PostRepository og controlleren. */

// Operatoren skal åbne opslag gennem administratorens route.
$operatorView = $operatorView ?? false;

$postUrl = $operatorView
    ? url('/admin/posts/show?id=' . (int) $post['id'])
    : url('/post?id=' . (int) $post['id']);
?>

<article class="polaroid post-card">

    <?php /* (int) sikrer, at opslagets ID bruges som et integer. */ ?>
    <a href="<?= e($postUrl) ?>" tabindex="-1" aria-hidden="true">
        <img
            class="polaroid__photo"
            src="<?= e($post['image']) ?>"
            alt=""
            width="800"
            height="600"
            loading="lazy">
    </a>

    <div class="polaroid__body">

        <p class="postmark postmark--date polaroid__postmark post-card__postmark">
            <time datetime="<?= e($post['date']) ?>">
                <?php
                // Dag og måned er allerede formateret i ExperienceController.
                // Vi viser dem på hver sin linje i frimærket.
                ?>
                <?= e($post['day']) ?><br><?= e($post['month']) ?>
            </time>
        </p>

        <h3 class="polaroid__title">
            <a class="polaroid__link" href="<?= e($postUrl) ?>">
                <?php
                // ?? betyder, at vi bruger en standardtekst, hvis placeringen mangler.
                // e() sørger for, at teksten vises sikkert i HTML.
                ?>
                <?= e($post['location'] ?? 'Uden placering') ?>
            </a>
        </h3>

        <p class="post-card__caption">
            <?php // Viser opslagets beskrivelse eller en tom tekst, hvis den mangler. ?>
            <?= e($post['caption'] ?? '') ?>
        </p>

        <p class="meta">
            <time datetime="<?= e($post['date']) ?>">
                <?php // Viser datoen i det format, controlleren har lavet. ?>
                <?= e($post['relative_date']) ?>
            </time>
        </p>

        <ul class="post-stats">

            <li class="post-stats__item">

                <?php if (!$operatorView): ?>
                    <?php /* Almindelige brugere kan like og unlike opslaget. */ ?>
                    <form action="<?= e(url('/post-like')) ?>" method="post" data-like-form>
                        <?= \PostCard\Core\Csrf::field() ?>
                        <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">

                        <button type="submit" class="like-button"
                                aria-pressed="<?= !empty($post['hasLiked']) ? 'true' : 'false' ?>"
                                aria-label="<?= !empty($post['hasLiked']) ? 'Fjern like' : 'Synes godt om' ?>">
                            <svg class="post-stats__icon" viewBox="0 0 24 24"
                                 aria-hidden="true" focusable="false">
                                <path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10Z" />
                            </svg>
                        </button>
                    </form>

                    <?php /* Åbner en oversigt over brugere, der har liket opslaget. */ ?>
                    <button
                        type="button"
                        class="like-count-button"
                        data-like-users
                        data-post-id="<?= (int) $post['id'] ?>"
                        data-url="<?= e(url('/post-likes?id=' . (int) $post['id'])) ?>"
                        aria-label="Se hvem der har liket opslaget">
                        <span data-like-count><?= (int) $post['likes'] ?></span> likes
                    </button>

                <?php else: ?>
                    <?php /* Operatoren kan se antal likes, men ikke like opslaget. */ ?>
                    <span><?= (int) $post['likes'] ?> likes</span>
                <?php endif; ?>

            </li>

            <li>
                <a class="post-stats__item" href="<?= e($postUrl) ?>#comments">
                    <svg class="post-stats__icon" viewBox="0 0 24 24"
                         aria-hidden="true" focusable="false">
                        <path d="M4 5h16v11H9l-5 4V5Z" />
                    </svg>

                    <?php
                    // Viser antallet af kommentarer fra databasen.
                    // Kommentarerne er talt i PostRepository.
                    ?>
                    <?= (int) $post['comments'] ?>
                    <span class="visually-hidden">kommentarer</span>
                </a>
            </li>

            <li class="post-stats__item">
                <?php
                // Viser antallet af registrerede visninger fra databasen.
                ?>
                Set af <?= (int) $post['views'] ?>
            </li>

        </ul>

    </div>
</article>
