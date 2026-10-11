<?php
$pageTitle = 'Indstillinger';
$isLoggedIn = true;
$pageScripts = ['avatar-preview'];
require __DIR__ . '/../views/partials/header.php';
?>

<main id="main" class="site-main">
    <div class="container container--text">
        <nav class="breadcrumb" aria-label="Du er her">
            <ol class="breadcrumb__list">
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="profile.php">Min profil</a></li>
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="settings.php" aria-current="page">Indstillinger</a></li>
            </ol>
        </nav>

        <header class="page-header">
            <p class="eyebrow">Min profil</p>
            <h1>Indstillinger</h1>
            <p>Ret dine oplysninger. Hver boks gemmes for sig.</p>
        </header>

        <!-- IF a form was saved: show the message from that form here
        <p class="alert" role="status">Dine oplysninger er gemt.</p>
        -->

        <!-- Three separate forms: each one changes one thing, so an error in one
             does not throw away what was typed in another -->
        <div class="card-stack">

            <section class="card" aria-labelledby="picture-heading">
                <h2 class="section-title" id="picture-heading">Profilbillede</h2>

                <!-- A CSRF token is added in Epic 2. enctype is needed to send a file. -->
                <form class="form" action="settings.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="form" value="picture">

                    <!-- IF users.profile_image: <img class="avatar avatar--large js-avatar-preview" src="…" alt=""> ELSE initials.
                         avatar-preview.js shows the chosen picture here before it is saved. -->
                    <span class="avatar avatar--large js-avatar-preview" aria-hidden="true">MN</span>

                    <div class="field">
                        <input class="visually-hidden file-picker__input js-avatar-input" id="profile-image" name="profile_image" type="file"
                            accept="image/jpeg,image/png,image/webp" required
                            aria-describedby="profile-image-hint">
                        <label class="file-picker__button" for="profile-image">
                            <svg class="file-picker__icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                <circle class="file-picker__circle" cx="12" cy="12" r="10" />
                                <path class="file-picker__plus" d="M12 7v10M7 12h10" />
                            </svg>
                            Vælg et nyt billede
                        </label>
                        <!-- The empty span is filled by avatar-preview.js, e.g. "Du har valgt ferie.jpg. Tryk Gem billede." -->
                        <p class="meta" id="profile-image-hint">JPG, PNG eller WebP, højst 5 MB. Uden billede viser vi dine initialer.
                            <span class="js-avatar-status" role="status"></span></p>
                    </div>

                    <div class="button-row">
                        <button class="button button--primary" type="submit">Gem billede</button>
                        <!-- IF users.profile_image is set.
                             form="remove-picture" sends the small form below instead of this one
                             (forms cannot be inside each other). -->
                        <button class="button button--secondary" type="submit" form="remove-picture">Fjern billede</button>
                        <!-- /IF -->
                    </div>
                </form>

                <!-- "Fjern billede": users.profile_image = NULL, the initials are shown again -->
                <form id="remove-picture" action="settings.php" method="post">
                    <input type="hidden" name="form" value="remove-picture">
                </form>
            </section>

            <section class="card" aria-labelledby="details-heading">
                <h2 class="section-title" id="details-heading">Mine oplysninger</h2>

                <!-- A CSRF token is added in Epic 2. The values come from the users table. -->
                <form class="form" action="settings.php" method="post">
                    <input type="hidden" name="form" value="details">

                    <div class="field-row">
                        <div class="field">
                            <label class="field__label" for="first-name">Fornavn</label>
                            <input class="field__input" id="first-name" name="first_name" type="text"
                                autocomplete="given-name" maxlength="100" required value="Mia">
                        </div>
                        <div class="field">
                            <label class="field__label" for="last-name">Efternavn</label>
                            <input class="field__input" id="last-name" name="last_name" type="text"
                                autocomplete="family-name" maxlength="100" required value="Nielsen">
                        </div>
                    </div>

                    <div class="field">
                        <label class="field__label" for="username">Brugernavn</label>
                        <input class="field__input" id="username" name="username" type="text"
                            autocomplete="username" maxlength="50" required value="mianielsen"
                            aria-describedby="username-hint">
                        <p class="meta" id="username-hint">Vises på din profil.</p>
                    </div>

                    <div class="field">
                        <label class="field__label" for="email">E-mail</label>
                        <input class="field__input" id="email" name="email" type="email"
                            autocomplete="email" maxlength="255" required value="mia@example.com"
                            aria-describedby="email-hint">
                        <!-- On a new e-mail: email_verified_at = NULL and a verify_email link (user_tokens) is sent -->
                        <p class="meta" id="email-hint">Skifter du e-mail, sender vi et link til den nye adresse, som du skal bekræfte.</p>
                    </div>

                    <div class="field">
                        <label class="field__label" for="birthdate">Fødselsdato</label>
                        <input class="field__input" id="birthdate" name="birthdate" type="date"
                            autocomplete="bday" required value="1990-04-18">
                    </div>

                    <div class="button-row">
                        <button class="button button--primary" type="submit">Gem oplysninger</button>
                    </div>
                </form>
            </section>

            <section class="card" aria-labelledby="password-heading">
                <h2 class="section-title" id="password-heading">Skift adgangskode</h2>

                <!-- A CSRF token is added in Epic 2. The current password proves it is really you. -->
                <form class="form" action="settings.php" method="post">
                    <input type="hidden" name="form" value="password">

                    <div class="field">
                        <label class="field__label" for="current-password">Nuværende adgangskode</label>
                        <input class="field__input" id="current-password" name="current_password" type="password"
                            autocomplete="current-password" required>
                    </div>

                    <div class="field">
                        <label class="field__label" for="new-password">Ny adgangskode</label>
                        <input class="field__input" id="new-password" name="new_password" type="password"
                            autocomplete="new-password" minlength="12" required
                            aria-describedby="new-password-hint">
                        <p class="meta" id="new-password-hint">Mindst 12 tegn. En sætning er nem at huske.</p>
                    </div>

                    <div class="field">
                        <label class="field__label" for="new-password-confirm">Gentag ny adgangskode</label>
                        <input class="field__input" id="new-password-confirm" name="new_password_confirm" type="password"
                            autocomplete="new-password" minlength="12" required>
                    </div>

                    <div class="button-row">
                        <button class="button button--primary" type="submit">Skift adgangskode</button>
                    </div>
                </form>
            </section>

        </div>
    </div>
</main>

<?php require __DIR__ . '/../views/partials/footer.php'; ?>
