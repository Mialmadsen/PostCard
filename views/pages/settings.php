<?php

/** @var array $user   what is saved: avatar */
/** @var array $old    what the fields show: the saved values, or what was typed */
/** @var array $errors one message per field that failed the check */
/** @var string|null $pictureError why the picture was not saved, or null */
/** @var array $passwordErrors one message per password field that failed the check */
/** @var string|null $settingsMessage the "saved" message after a redirect, or null */

?>

<main id="main" class="site-main">
    <div class="container container--text">
        <?php
        $breadcrumbs = [
            ['label' => 'Min profil', 'path' => '/profile'],
            ['label' => 'Indstillinger', 'path' => '/settings'],
        ];
        require __DIR__ . '/../partials/breadcrumbs.php';
        ?>

        <header class="page-header">
            <p class="eyebrow">Min profil</p>
            <h1>Indstillinger</h1>
            <p>Ret dine oplysninger. Hver boks gemmes for sig.</p>
        </header>

        <?php if ($settingsMessage !== null): ?>
            <p class="alert" role="status"><?= e($settingsMessage) ?></p>
        <?php endif; ?>

        <!-- Three separate forms: each one changes one thing, so an error in one
             does not throw away what was typed in another -->
        <div class="card-stack">

            <section class="card" aria-labelledby="picture-heading">
                <h2 class="section-title" id="picture-heading">Profilbillede</h2>

                <!-- enctype is needed to send a file. -->
                <form class="form" action="<?= url('/settings/picture') ?>" method="post" enctype="multipart/form-data">
                    <?= \PostCard\Core\Csrf::field() ?>

                    <!-- avatar-preview.js shows the chosen picture here before it is saved -->
                    <?php
                    $avatarUser = $user;
                    $avatarClass = 'avatar--large js-avatar-preview';
                    require __DIR__ . '/../partials/avatar.php';
                    ?>

                    <div class="field">
                        <input class="visually-hidden file-picker__input js-avatar-input" id="profile-image" name="profile_image" type="file"
                            accept="image/jpeg,image/png,image/webp" required
                            <?php if ($pictureError !== null): ?>aria-invalid="true"
                            aria-describedby="profile-image-hint profile-image-error"
                            <?php else: ?>aria-describedby="profile-image-hint" <?php endif; ?>>
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
                        <?php if ($pictureError !== null): ?>
                            <p class="field__error" id="profile-image-error"><?= e($pictureError) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="button-row">
                        <button class="button button--primary" type="submit">Gem billede</button>
                        <!-- form="remove-picture" sends the small form below instead of this one
                             (forms cannot be inside each other). -->
                        <?php if ($user['profile_image'] !== null): ?>
                            <button class="button button--secondary" type="submit" form="remove-picture">Fjern billede</button>
                        <?php endif; ?>
                    </div>
                </form>

                <!-- "Fjern billede": the three picture columns become NULL, the initials are shown again -->
                <?php if ($user['profile_image'] !== null): ?>
                    <form id="remove-picture" action="<?= url('/settings/remove-picture') ?>" method="post">
                        <?= \PostCard\Core\Csrf::field() ?>
                    </form>
                <?php endif; ?>
            </section>

            <section class="card" aria-labelledby="details-heading">
                <h2 class="section-title" id="details-heading">Mine oplysninger</h2>

                <?php if ($errors !== []): ?>
                    <p class="alert alert--error" role="alert">Noget mangler eller er forkert. Se de markerede felter
                        nedenfor.</p>
                <?php endif; ?>

                <!-- The values come from the users table. -->
                <form class="form" action="<?= url('/settings/details') ?>" method="post">
                    <?= \PostCard\Core\Csrf::field() ?>

                    <div class="field-row">
                        <div class="field">
                            <label class="field__label" for="first-name">Fornavn</label>
                            <input class="field__input" id="first-name" name="first_name" type="text"
                                autocomplete="given-name" maxlength="100" required
                                value="<?= e($old['first_name'] ?? '') ?>"
                                <?php if (isset($errors['first_name'])): ?>aria-invalid="true" aria-describedby="first-name-error"
                                <?php endif; ?>>
                            <?php if (isset($errors['first_name'])): ?>
                                <p class="field__error" id="first-name-error"><?= e($errors['first_name']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="field">
                            <label class="field__label" for="last-name">Efternavn</label>
                            <input class="field__input" id="last-name" name="last_name" type="text"
                                autocomplete="family-name" maxlength="100" required
                                value="<?= e($old['last_name'] ?? '') ?>"
                                <?php if (isset($errors['last_name'])): ?>aria-invalid="true" aria-describedby="last-name-error"
                                <?php endif; ?>>
                            <?php if (isset($errors['last_name'])): ?>
                                <p class="field__error" id="last-name-error"><?= e($errors['last_name']) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="field">
                        <label class="field__label" for="username">Brugernavn</label>
                        <input class="field__input" id="username" name="username" type="text"
                            autocomplete="username" maxlength="50" required
                            value="<?= e($old['username'] ?? '') ?>"
                            <?php if (isset($errors['username'])): ?>aria-invalid="true"
                            aria-describedby="username-hint username-error"
                            <?php else: ?>aria-describedby="username-hint" <?php endif; ?>>
                        <p class="meta" id="username-hint">Vises på din profil.</p>
                        <?php if (isset($errors['username'])): ?>
                            <p class="field__error" id="username-error"><?= e($errors['username']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="field">
                        <label class="field__label" for="email">E-mail</label>
                        <input class="field__input" id="email" name="email" type="email"
                            autocomplete="email" maxlength="255" required
                            value="<?= e($old['email'] ?? '') ?>"
                            <?php if (isset($errors['email'])): ?>aria-invalid="true"
                            aria-describedby="email-hint email-error"
                            <?php else: ?>aria-describedby="email-hint" <?php endif; ?>>
                        <!-- On a new e-mail: email_verified_at = NULL and a verify_email link (user_tokens) is sent -->
                        <p class="meta" id="email-hint">Skifter du e-mail, sender vi et link til den nye adresse, som du skal bekræfte.</p>
                        <?php if (isset($errors['email'])): ?>
                            <p class="field__error" id="email-error"><?= e($errors['email']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="field">
                        <label class="field__label" for="birthdate">Fødselsdato</label>
                        <input class="field__input" id="birthdate" name="birthdate" type="date"
                            autocomplete="bday" required
                            value="<?= e($old['birthdate'] ?? '') ?>"
                            <?php if (isset($errors['birthdate'])): ?>aria-invalid="true" aria-describedby="birthdate-error"
                            <?php endif; ?>>
                        <?php if (isset($errors['birthdate'])): ?>
                            <p class="field__error" id="birthdate-error"><?= e($errors['birthdate']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="button-row">
                        <button class="button button--primary" type="submit">Gem oplysninger</button>
                    </div>
                </form>
            </section>

            <section class="card" aria-labelledby="password-heading">
                <h2 class="section-title" id="password-heading">Skift adgangskode</h2>

                <!-- The current password proves it is really you. -->
                <!-- The password fields never get a value="": a password is never printed into the page -->
                <form class="form" action="<?= url('/settings/password') ?>" method="post">
                    <?= \PostCard\Core\Csrf::field() ?>

                    <div class="field">
                        <label class="field__label" for="current-password">Nuværende adgangskode</label>
                        <input class="field__input" id="current-password" name="current_password" type="password"
                            autocomplete="current-password" required
                            <?php if (isset($passwordErrors['current_password'])): ?>aria-invalid="true" aria-describedby="current-password-error"
                            <?php endif; ?>>
                        <?php if (isset($passwordErrors['current_password'])): ?>
                            <p class="field__error" id="current-password-error"><?= e($passwordErrors['current_password']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="field">
                        <label class="field__label" for="new-password">Ny adgangskode</label>
                        <input class="field__input" id="new-password" name="password" type="password"
                            autocomplete="new-password" minlength="12" required
                            <?php if (isset($passwordErrors['password'])): ?>aria-invalid="true"
                            aria-describedby="new-password-hint new-password-error"
                            <?php else: ?>aria-describedby="new-password-hint" <?php endif; ?>>
                        <p class="meta" id="new-password-hint">Mindst 12 tegn. En sætning er nem at huske.</p>
                        <?php if (isset($passwordErrors['password'])): ?>
                            <p class="field__error" id="new-password-error"><?= e($passwordErrors['password']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="field">
                        <label class="field__label" for="new-password-confirm">Gentag ny adgangskode</label>
                        <input class="field__input" id="new-password-confirm" name="password_confirm" type="password"
                            autocomplete="new-password" minlength="12" required
                            <?php if (isset($passwordErrors['password_confirm'])): ?>aria-invalid="true" aria-describedby="new-password-confirm-error"
                            <?php endif; ?>>
                        <?php if (isset($passwordErrors['password_confirm'])): ?>
                            <p class="field__error" id="new-password-confirm-error"><?= e($passwordErrors['password_confirm']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="button-row">
                        <button class="button button--primary" type="submit">Skift adgangskode</button>
                    </div>
                </form>
            </section>

            <section class="card" aria-labelledby="delete-heading">
                <h2 class="section-title" id="delete-heading">Slet bruger</h2>

                <!-- The rules page promises this ("Du kan altid slette din bruger").
                     Sets users.user_status = 'deleted', deletes the user's posts
                     and logs out. The password proves it is really you; confirm.js asks once more. -->
                <form class="form js-confirm" action="<?= url('/settings') ?>" method="post"
                    data-confirm="Vil du slette din bruger? Dine oplysninger og dine opslag bliver slettet. Det kan ikke fortrydes.">
                    <?= \PostCard\Core\Csrf::field() ?>
                    <input type="hidden" name="form" value="delete">

                    <p>Dine oplysninger og dine opslag bliver slettet, og du kan ikke logge ind igen.</p>

                    <div class="field">
                        <label class="field__label" for="delete-password">Din adgangskode</label>
                        <input class="field__input" id="delete-password" name="password" type="password"
                            autocomplete="current-password" required>
                    </div>

                    <div class="button-row">
                        <button class="button button--secondary" type="submit">Slet min bruger</button>
                    </div>
                </form>
            </section>

        </div>
    </div>
</main>
