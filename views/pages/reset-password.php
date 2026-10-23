<?php
// The controller shows this page with:
// View::render('reset-password', ['pageTitle' => 'Ny adgangskode', 'isLoggedIn' => false]);
?>

<main id="main" class="site-main">
    <div class="container">
        <div class="envelope envelope--still">
            <div class="envelope__back" aria-hidden="true"></div>
            <div class="envelope__letter flow">
                <p class="eyebrow">Ny adgangskode</p>
                <h1>Vælg en ny adgangskode</h1>

                <!-- IF the link is expired or used: <p class="alert alert--error" role="alert">Linket er udløbet. <a href="<?= url('/forgot-password') ?>">Bed om et nyt link</a>.</p> -->

                <!-- The reset token from the link goes in a hidden field -->
                <form class="form" action="<?= url('/reset-password') ?>" method="post">
                    <?= \PostCard\Core\Csrf::field() ?>
                    <div class="field">
                        <label class="field__label" for="password">Ny adgangskode</label>
                        <input class="field__input" id="password" name="password" type="password"
                            autocomplete="new-password" minlength="12" required
                            aria-describedby="password-hint">
                        <p class="meta" id="password-hint">Mindst 12 tegn.</p>
                    </div>

                    <div class="field">
                        <label class="field__label" for="password-confirm">Gentag ny adgangskode</label>
                        <input class="field__input" id="password-confirm" name="password_confirm" type="password"
                            autocomplete="new-password" minlength="12" required>
                    </div>

                    <div class="form__actions">
                        <button class="button button--primary" type="submit">Gem ny adgangskode</button>
                    </div>
                </form>
            </div>
            <div class="envelope__front" aria-hidden="true"></div>
        </div>
    </div>
</main>
