<?php
$pageTitle = 'Rediger opslag';
$isLoggedIn = true;
$pageStyle = 'create-post';
$pageScripts = ['photo-preview'];
require __DIR__ . '/../views/partials/header.php';
?>

<!-- The same form as create-post.php, with the post's values filled in.
     Only the owner of the post can open this page (experiences.user_id = me). The server checks this. -->

<main id="main" class="site-main">
    <div class="container">
        <nav class="breadcrumb" aria-label="Du er her">
            <ol class="breadcrumb__list">
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="home.php">Mine grupper</a></li>
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="group.php?id=1">Familien Nielsen</a></li>
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="experience.php?id=1">Sommer i Norge</a></li>
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="post.php?id=6">Prækestolen, Norge</a></li>
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="edit-post.php?id=6" aria-current="page">Rediger</a></li>
            </ol>
        </nav>

        <header class="page-header">
            <p class="group-label">Familien Nielsen · Sommer i Norge</p>
            <h1>Rediger opslag</h1>
        </header>

        <div class="sidebar-layout">

            <!-- Left (first on phones): where the post is shown -->
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
                </dl>
            </aside>

            <section class="card" aria-labelledby="form-heading">
                <h2 class="section-title" id="form-heading">Dit opslag</h2>

                <!-- A CSRF token is added in Epic 2. enctype is needed to send files. -->
                <form class="form" action="edit-post.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="post_id" value="6">

                    <div class="field">
                        <p class="field__label">Vis det</p>
                        <!-- Not "required" here: the post already has photos.
                             photo-preview.js makes it required if all saved photos are removed. -->
                        <input class="visually-hidden file-picker__input js-photo-input" id="photos" name="photos[]" type="file"
                            accept="image/jpeg,image/png,image/webp" multiple
                            aria-describedby="photos-hint">
                        <label class="file-picker__button" for="photos">
                            <svg class="file-picker__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                <circle class="file-picker__circle" cx="12" cy="12" r="10" />
                                <path class="file-picker__plus" d="M12 7v10M7 12h10" />
                            </svg>
                            Tilføj billeder
                        </label>
                        <p class="meta" id="photos-hint">1–5 billeder i alt i JPG, PNG eller WebP, højst 10 MB hver. Du vælger selv forsidebilledet.
                            <span class="js-photo-status" role="status"></span></p>
                    </div>

                    <!-- LOOP: the saved photos (post_images, ordered by sort_order; the first is the cover).
                         data-image-id tells photo-preview.js which photo it is. The script then draws
                         the list again from the <template> below, so saved and new photos look the same. -->
                    <ul class="photo-preview js-photo-preview">
                        <li class="polaroid photo-preview__item" data-image-id="11">
                            <img class="polaroid__photo" src="assets/images/sample/photo-4.jpg" alt="" width="800" height="600">
                            <input class="js-photo-alt" type="hidden"
                                value="Udsigt fra Prækestolen ned over Lysefjorden med stejle klipper og blåt vand.">
                        </li>
                        <li class="polaroid photo-preview__item" data-image-id="12">
                            <img class="polaroid__photo" src="assets/images/sample/photo-1.jpg" alt="" width="800" height="600">
                            <input class="js-photo-alt" type="hidden" value="Granskov og en høj klippevæg ved en stille sø.">
                        </li>
                        <li class="polaroid photo-preview__item" data-image-id="13">
                            <img class="polaroid__photo" src="assets/images/sample/photo-3.jpg" alt="" width="800" height="600">
                            <input class="js-photo-alt" type="hidden" value="Et vandfald der falder ned i en grøn kløft.">
                        </li>
                    </ul>
                    <!-- /LOOP -->

                    <div class="field">
                        <label class="field__label" for="location">Sted</label>
                        <input class="field__input" id="location" name="location" type="text"
                            maxlength="150" value="Prækestolen, Norge" aria-describedby="location-hint">
                        <p class="meta" id="location-hint">Fx Prækestolen, Norge eller hjemme i haven</p>
                    </div>

                    <div class="field">
                        <label class="field__label" for="caption">Fortæl om det</label>
                        <textarea class="field__input" id="caption" name="caption" rows="5" maxlength="2000"
                            aria-describedby="caption-hint">Sidste dag i fjordene. Vi gik op til Prækestolen igen for at sige farvel til udsigten. Fire timer op og ned, ømme ben – og det var det hele værd. Tak for en fantastisk tur, alle sammen!</textarea>
                        <p class="meta" id="caption-hint">Det, gruppen læser under billederne</p>
                    </div>

                    <!-- Answers "What happens when I save?" before the button -->
                    <p class="alert">Ændringerne bliver vist med det samme for medlemmerne af <strong>Familien Nielsen</strong>. Billeder, du har fjernet, bliver slettet. Det tæller ikke som et nyt opslag.</p>

                    <div class="button-row">
                        <button class="button button--primary" type="submit">Gem ændringer</button>
                        <a class="button button--secondary" href="post.php?id=6">Annuller</a>
                    </div>
                </form>
            </section>

        </div>
    </div>
</main>

<!-- One photo item, the same as in create-post.php. photo-preview.js copies it for each photo.
     Later both pages share it as a partial. -->
<template class="js-photo-template">
    <li class="polaroid photo-preview__item">
        <img class="polaroid__photo" src="" alt="">
        <div class="field field--checkbox">
            <input class="field__checkbox js-photo-cover" name="cover" type="radio">
            <label class="js-photo-cover-label">Forsidebillede</label>
        </div>
        <details class="photo-preview__details">
            <summary class="photo-preview__toggle">Beskriv billedet for blinde og svagsynede (valgfrit)</summary>
            <div class="field">
                <label class="field__label js-photo-alt-label">Beskrivelse</label>
                <input class="field__input js-photo-alt" name="alt_text[]" type="text" maxlength="255">
            </div>
        </details>
        <button class="button button--secondary photo-preview__remove js-photo-remove" type="button">
            Fjern <span class="visually-hidden">billede</span>
        </button>
    </li>
</template>

<?php require __DIR__ . '/../views/partials/footer.php'; ?>
