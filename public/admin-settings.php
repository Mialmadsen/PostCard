<?php
$pageTitle = 'Administration · Indstillinger';
$isOperator = true;
$pageStyle = 'admin';
$adminPage = 'settings';
require __DIR__ . '/../views/partials/header.php';
?>

<!-- Site-wide settings (site_settings: one row per setting_key).
     PHP checks every value before saving it, e.g. that numbers are whole numbers in range. -->

<main id="main" class="site-main">
    <div class="container">
        <p class="eyebrow">Administration</p>
        <h1>Indstillinger</h1>
        <p>Gælder hele <span class="brand">PostCard</span> og alle grupper.</p>

        <!-- IF saved: <p class="alert" role="status">Indstillingerne er gemt.</p> -->

        <div class="sidebar-layout">
            <?php require __DIR__ . '/../views/partials/admin-nav.php'; ?>

            <section class="card" aria-labelledby="settings-heading">
                <h2 class="visually-hidden" id="settings-heading">Indstillinger</h2>

                <!-- A CSRF token is added in Epic 2 -->
                <form class="form" action="admin-settings.php" method="post">

                    <!-- setting_key = 'default_post_limit' -->
                    <div class="field">
                        <label class="field__label" for="default-post-limit">Antal opslag pr. oplevelse</label>
                        <input class="field__input field__input--short" id="default-post-limit" name="default_post_limit" type="number"
                            min="1" max="50" value="10" required aria-describedby="default-post-limit-hint">
                        <p class="meta" id="default-post-limit-hint">Forslaget, når nogen opretter en ny gruppe. Gruppens admin kan ændre det.</p>
                    </div>

                    <!-- setting_key = 'max_upload_size' (saved as e.g. "10MB") -->
                    <div class="field">
                        <label class="field__label" for="max-upload-size">Største billede i et opslag (MB)</label>
                        <input class="field__input field__input--short" id="max-upload-size" name="max_upload_size" type="number"
                            min="1" max="20" value="10" required aria-describedby="max-upload-size-hint">
                        <p class="meta" id="max-upload-size-hint">Højst 20 MB. Serveren skal også tillade størrelsen.</p>
                    </div>

                    <!-- setting_key = 'allow_likes' and 'allow_comments' (1 or 0).
                         An unchecked box sends nothing, so PHP saves 0 when the field is missing. -->
                    <fieldset class="fieldset">
                        <legend class="field__label fieldset__legend">Funktioner</legend>
                        <div class="field field--checkbox">
                            <input class="field__checkbox" id="allow-likes" name="allow_likes" type="checkbox" value="1" checked
                                aria-describedby="allow-likes-hint">
                            <div>
                                <label for="allow-likes">Synes godt om</label>
                                <p class="meta" id="allow-likes-hint">Slået fra: hjertet og Populært i dine grupper bliver skjult.</p>
                            </div>
                        </div>
                        <div class="field field--checkbox">
                            <input class="field__checkbox" id="allow-comments" name="allow_comments" type="checkbox" value="1" checked
                                aria-describedby="allow-comments-hint">
                            <div>
                                <label for="allow-comments">Kommentarer</label>
                                <p class="meta" id="allow-comments-hint">Slået fra: ingen kan skrive nye kommentarer. De gamle bliver stående.</p>
                            </div>
                        </div>
                    </fieldset>

                    <div class="button-row">
                        <button class="button button--primary" type="submit">Gem indstillinger</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../views/partials/footer.php'; ?>
