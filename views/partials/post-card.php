<!-- filen bruges til at vise hvert opslag i vores experience. 
 der bruges en foreach-loop, som indlæser partial for hvert opslag. 
 Infoen kommer fra databasen gennem PostRepository og controlleren -->

 <article class="polaroid post-card">

<!-- (int) sikrer, at opslagets ID bruges som et integer -->
 <!-- ENT_QUOTES – Sørger for, at både enkelte og dobbelte anførselstegn bliver omdannet. -->
<a href="<?= url('/post') ?>?id=<?= (int) $post['id'] ?>" tabindex="-1" aria-hidden="true">
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
            // Dag og måned er allerede formateret i ExperienceController
            // Vi viser dem på hver sin linje i frimærket
            ?>
            <?= e($post['day']) ?><br><?= e($post['month']) ?>
        </time>
    </p>

    <h3 class="polaroid__title">
        <a class="polaroid__link" href="<?= url('/post') ?>?id=<?= (int) $post['id'] ?>">
            <?php
            // ?? betyder, at vi bruger en standardtekst, hvis placeringen mangler
            // htmlspecialchars() sikrer, at teksten vises sikkert i HTML
            ?>
            <?= e($post['location'] ?? 'Uden placering') ?>
        </a>
    </h3>

    <p class="post-card__caption">
        <?php // Viser opslagets beskrivelse eller en tom tekst, hvis den mangler ?>
        <?= e($post['caption'] ?? '') ?>
    </p>

    <p class="meta">
        <time datetime="<?= e($post['date']) ?>">
            <?php // Viser datoen i det format, controlleren har lavet ?>
            <?= e($post['relative_date']) ?>
        </time>
    </p>

    <ul class="post-stats">

        <li>
            <button
                class="post-stats__item like-button"
                type="button"
                aria-pressed="false"
                disabled>

                <svg class="post-stats__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M12 20s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 10c0 5.6-7 10-7 10Z" />
                </svg>

                <?php
                // Viser antallet af likes, som PostRepository har hentet fra databasen
                // (int) sikrer, at antallet bliver vist som et heltal
                ?>
                <?= (int) $post['likes'] ?>
                <span class="visually-hidden">synes godt om</span>
            </button>
        </li>

        <li>
            <a class="post-stats__item" href="<?= url('/post') ?>?id=<?= (int) $post['id'] ?>#comments">

                <svg class="post-stats__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                    <path d="M4 5h16v11H9l-5 4V5Z" />
                </svg>

                <?php
                // Viser antallet af kommentarer fra databasen
                // Kommentarerne er talt i PostRepository
                ?>
                <?= (int) $post['comments'] ?>
                <span class="visually-hidden">kommentarer</span>
            </a>
        </li>

        <li class="post-stats__item">
            <?php
            // Viser antallet af registrerede visninger fra databasen
            // Antallet bliver endnu ikke automatisk opdateret ved besøg
            ?>
            Set af <?= (int) $post['views'] ?>
        </li>

    </ul>

</div>
</article>
