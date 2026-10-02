<?php
$pageTitle = 'Nyt opslag';
$isLoggedIn = true;
$pageStyle = 'create-post';
$pageScripts = ['photo-preview'];
require __DIR__ . '/../views/partials/header.php';
?>

<main id="main" class="site-main">
    <div class="container">
        <nav class="breadcrumb" aria-label="Du er her">
            <ol class="breadcrumb__list">
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="home.php">Mine grupper</a></li>
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="group.php?id=1">Familien Nielsen</a></li>
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="experience.php?id=1">Sommer i Norge</a></li>
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="create-post.php?experience=1" aria-current="page">Nyt opslag</a></li>
            </ol>
        </nav>

        <header class="page-header">
            <p class="group-label">Familien Nielsen · Sommer i Norge</p>
            <h1>Nyt opslag</h1>
        </header>

        <!-- IF the experience is not active, or all posts are used:
             show only this message instead of the form (the server checks this again)
        <p class="alert alert--error">Du kan ikke lave flere opslag i Sommer i Norge. Oplevelsen er afsluttet.</p>
        -->

        <div class="sidebar-layout">

            <!-- Left (first on phones): who receives the post, answered before the form -->
            <aside class="card flow" aria-labelledby="audience-heading">
                <h2 id="audience-heading">Til</h2>
                <dl class="address">
                    <div>
                        <dt class="group-label">Gruppe</dt>
                        <dd class="address__value">Familien Nielsen</dd>
                    </div>
                    <div>
                        <dt class="group-label">Oplevelse</dt>
                        <dd class="address__value">Sommer i Norge</dd>
                    </div>
                    <div>
                        <dt class="group-label">Opslag tilbage</dt>
                        <dd class="address__value">14 af 20 · slutter <time datetime="2026-10-15">15. okt. 2026</time></dd>
                    </div>
                </dl>
            </aside>

            <!-- Right: the form. enctype is needed to send files.
                 Later edit-post.php reuses this form with the values filled in. -->
            <section class="card" aria-labelledby="form-heading">
                <h2 class="section-title" id="form-heading">Dit opslag</h2>

                <!-- A CSRF token is added in Epic 2 -->
                <form class="form" action="create-post.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="experience_id" value="1">

                    <div class="field">
                        <!-- "Vis det" is only a visible title (pairs with "Fortæl om det" below).
                             The input's name is the label below: the real file input is hidden,
                             and its <label> is what you see and click. -->
                        <p class="field__label">Vis det</p>
                        <input class="visually-hidden file-picker__input js-photo-input" id="photos" name="photos[]" type="file"
                            accept="image/jpeg,image/png,image/webp" multiple required
                            aria-describedby="photos-hint">
                        <label class="file-picker__button" for="photos">
                            <svg class="file-picker__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                <circle class="file-picker__circle" cx="12" cy="12" r="10" />
                                <path class="file-picker__plus" d="M12 7v10M7 12h10" />
                            </svg>
                            Tilføj billeder
                        </label>
                        <!-- The empty span is filled by photo-preview.js ("2 af 5 billeder valgt.");
                             role="status" makes screen readers read it when it changes -->
                        <p class="meta" id="photos-hint">1–5 billeder i JPG, PNG eller WebP, højst 10 MB hver. Det første billede bliver forsidebillede.
                            <span class="js-photo-status" role="status"></span></p>
                    </div>

                    <!-- Filled by photo-preview.js: one small polaroid per chosen photo -->
                    <ul class="photo-preview js-photo-preview"></ul>

                    <div class="field">
                        <label class="field__label" for="location">Sted</label>
                        <input class="field__input" id="location" name="location" type="text"
                            maxlength="150" aria-describedby="location-hint">
                        <p class="meta" id="location-hint">Fx Prækestolen, Norge eller hjemme i haven</p>
                    </div>

                    <div class="field">
                        <label class="field__label" for="caption">Fortæl om det</label>
                        <textarea class="field__input" id="caption" name="caption" rows="5" maxlength="2000"
                            aria-describedby="caption-hint"></textarea>
                        <p class="meta" id="caption-hint">Det, gruppen læser under billederne</p>
                    </div>

                    <!-- Answers "What happens when I publish?" before the button -->
                    <p class="alert">Opslaget bliver vist i <strong>Sommer i Norge</strong> og kan kun ses af medlemmerne af <strong>Familien Nielsen</strong>. Det bruger 1 af dine 14 opslag.</p>

                    <div class="button-row">
                        <button class="button button--primary" type="submit">Udgiv opslag</button>
                        <a class="button button--secondary" href="experience.php?id=1">Annuller</a>
                    </div>
                </form>
            </section>

        </div>
    </div>
</main>

<!-- One preview item. photo-preview.js copies it for each photo and fills in the numbers. -->
<template class="js-photo-template">
    <li class="polaroid photo-preview__item">
        <img class="polaroid__photo" src="" alt="">
        <p class="group-label js-photo-cover">Forsidebillede</p>
        <!-- Optional alt text (post_images.alt_text). <details> opens and closes without JavaScript.
             If it is left empty, PHP writes a fallback in Epic 2, e.g. "Billede 1 af 3 fra Prækestolen, Norge". -->
        <details class="photo-preview__details">
            <summary class="photo-preview__toggle">Beskriv billedet for blinde og svagsynede (valgfrit)</summary>
            <div class="field">
                <label class="field__label">Beskrivelse</label>
                <input class="field__input" name="alt_text[]" type="text" maxlength="255">
            </div>
        </details>
        <button class="button button--secondary photo-preview__remove" type="button">
            Fjern <span class="visually-hidden">billede</span>
        </button>
    </li>
</template>

<?php require __DIR__ . '/../views/partials/footer.php'; ?>
