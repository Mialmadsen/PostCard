<main id="main" class="site-main">
    <div class="container">

        <?php
        // Opretter breadcrumbs til siden, hvor brugeren laver et nyt opslag.
        $breadcrumbs = [
            ['label' => 'Mine grupper', 'path' => '/home'],
            ['label' => $experience['group_name'], 'path' => '/group?id=' . (int) $experience['group_id']],
            ['label' => $experience['name'], 'path' => '/experience?id=' . (int) $experience['experience_id']],
            ['label' => 'Nyt opslag', 'path' => '/create-post?experience=' . (int) $experience['experience_id']],
        ];

        // Indlæser den fælles breadcrumbs-fil.
        require __DIR__ . '/../partials/breadcrumbs.php';
        ?>

        <header class="page-header">
            <p class="group-label">
                <?= e($experience['group_name']) ?> · <?= e($experience['name']) ?>
            </p>
            <h1>Nyt opslag</h1>
        </header>

        <?php /* IF the experience is not active, or all posts are used:
             show only this message instead of the form (the server checks this again)
        <p class="alert alert--error">Du kan ikke lave flere opslag i Sommer i Norge. Oplevelsen er afsluttet.</p>
        */ ?>

        <div class="sidebar-layout">

            <?php /* Left (first on phones): who receives the post, answered before the form */ ?>
            <aside class="card flow" aria-labelledby="audience-heading">
                <h2 id="audience-heading">Til</h2>

                <dl class="address">

                    <div>
                        <dt class="group-label">Gruppe</dt>
                        <dd class="address__value">
                            <?= e($experience['group_name']) ?>
                        </dd>
                    </div>

                    <div>
                        <dt class="group-label">Oplevelse</dt>
                        <dd class="address__value">
                            <?= e($experience['name']) ?>
                        </dd>
                    </div>

                    <div>
                        <dt class="group-label">Opslag tilbage</dt>
                        <dd class="address__value">

                            <?php if ($postCounts['postsRemaining'] === null): ?>
                                Ubegrænset antal opslag
                            <?php else: ?>
                                <?= e((string) $postCounts['postsRemaining']) ?>
                                af <?= e((string) $postCounts['postLimit']) ?>
                            <?php endif; ?>

                            · slutter
                            <time datetime="<?= e($experience['end_date']) ?>">
                                <?= e(danishDate($experience['end_date'], 'd. MMM y')) ?>
                            </time>

                        </dd>
                    </div>

                </dl>
            </aside>

            <?php /* Right: the form. enctype is needed to send files. */ ?>
            <section class="card" aria-labelledby="form-heading">
                <h2 class="section-title" id="form-heading">Dit opslag</h2>

                <?php
                // Viser en fejl direkte i formularen, hvis et billede
                // ikke overholder uploadkravene.
                ?>
                <?php if (!empty($uploadError)): ?>
                    <p class="alert alert--error" role="alert">
                        <?= e($uploadError) ?>
                        Vælg venligst dine billeder igen.
                    </p>
                <?php endif; ?>

                <form
                    class="form"
                    action="<?= e(url('/create-post')) ?>"
                    method="post"
                    enctype="multipart/form-data"
                >
                    <?= \PostCard\Core\Csrf::field() ?>

                    <input
                        type="hidden"
                        name="experience_id"
                        value="<?= e((string) $experience['experience_id']) ?>"
                    >

                    <div class="field">
                        <?php
                        // "Vis det" er den synlige overskrift til billedfeltet.
                        // Selve filinputtet er skjult og åbnes via label.
                        ?>
                        <p class="field__label">Vis det</p>

                        <input
                            class="visually-hidden file-picker__input js-photo-input"
                            id="photos"
                            name="photos[]"
                            type="file"
                            accept="image/jpeg,image/png,image/webp"
                            multiple
                            required
                            aria-describedby="photos-hint"
                        >

                        <label class="file-picker__button" for="photos">
                            <svg
                                class="file-picker__icon"
                                viewBox="0 0 24 24"
                                aria-hidden="true"
                                focusable="false"
                            >
                                <circle class="file-picker__circle" cx="12" cy="12" r="10" />
                                <path class="file-picker__plus" d="M12 7v10M7 12h10" />
                            </svg>
                            Tilføj billeder
                        </label>

                        <?php
                        // photo-preview.js opdaterer antallet af valgte billeder.
                        ?>
                        <p class="meta" id="photos-hint">
                            1–5 billeder i JPG, PNG eller WebP.
                            Højst 5 MB, 8000 pixels i bredde eller højde
                            og 30 millioner pixels i alt pr. billede.
                            Du vælger selv forsidebilledet.
                            <span class="js-photo-status" role="status"></span>
                        </p>
                    </div>

                    <?php
                    // photo-preview.js opretter et preview for hvert valgt billede.
                    ?>
                    <ul class="photo-preview js-photo-preview"></ul>

                    <div class="field">
                        <label class="field__label" for="location">Sted</label>

                        <input
                            class="field__input"
                            id="location"
                            name="location"
                            type="text"
                            maxlength="150"
                            aria-describedby="location-hint"
                            value="<?= e($oldLocation ?? '') ?>"
                        >

                        <p class="meta" id="location-hint">
                            Fx Prækestolen, Norge eller hjemme i haven
                        </p>
                    </div>

                    <div class="field">
                        <label class="field__label" for="caption">Fortæl om det</label>

                        <textarea
                            class="field__input"
                            id="caption"
                            name="caption"
                            rows="5"
                            maxlength="2000"
                            aria-describedby="caption-hint"
                        ><?= e($oldCaption ?? '') ?></textarea>

                        <p class="meta" id="caption-hint">
                            Det, gruppen læser under billederne
                        </p>
                    </div>

                    <?php /* Answers "What happens when I publish?" before the button */ ?>
                    <p class="alert">
                        Opslaget bliver vist i
                        <strong><?= e($experience['name']) ?></strong>
                        og kan kun ses af medlemmerne af
                        <strong><?= e($experience['group_name']) ?></strong>.

                        <?php if ($postCounts['postsRemaining'] === null): ?>
                            Der er ingen opslaggrænse for denne oplevelse.
                        <?php else: ?>
                            Det bruger 1 af dine
                            <?= e((string) $postCounts['postsRemaining']) ?>
                            resterende opslag.
                        <?php endif; ?>
                    </p>

                    <div class="button-row">
                        <button
                            class="button button--primary"
                            type="submit"
                        >
                            Udgiv opslag
                        </button>

                        <a
                            class="button button--secondary"
                            href="<?= e(url('/experience?id=' . (int) $experience['experience_id'])) ?>"
                        >
                            Annuller
                        </a>
                    </div>

                </form>
            </section>

        </div>
    </div>
</main>

<?php
// photo-preview.js kopierer denne template for hvert valgt billede.
?>
<template class="js-photo-template">
    <li class="polaroid photo-preview__item">
        <img class="polaroid__photo" src="" alt="">

        <?php
        // Det valgte forsidebillede får sort_order 1.
        // photo-preview.js sætter id, value og checked.
        ?>
        <div class="field field--checkbox">
            <input
                class="field__checkbox js-photo-cover"
                name="cover"
                type="radio"
            >
            <label class="js-photo-cover-label">
                Forsidebillede
            </label>
        </div>

        <?php
        // Valgfri alt-tekst til billedet.
        ?>
        <details class="photo-preview__details">
            <summary class="photo-preview__toggle">
                Beskriv billedet for blinde og svagsynede (valgfrit)
            </summary>

            <div class="field">
                <label class="field__label js-photo-alt-label">
                    Beskrivelse
                </label>

                <input
                    class="field__input js-photo-alt"
                    name="alt_text[]"
                    type="text"
                    maxlength="255"
                >
            </div>
        </details>

        <button
            class="button button--secondary photo-preview__remove js-photo-remove"
            type="button"
        >
            Fjern <span class="visually-hidden">billede</span>
        </button>
    </li>
</template>
