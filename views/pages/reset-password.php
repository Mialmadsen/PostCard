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

                <?php /* IF the link is expired or used: <p class="alert alert--error" role="alert">Linket er udløbet. <a href="<?= url('/forgot-password') ?>">Bed om et nyt link</a>.</p> */ ?>

                <?php /* The reset token from the link goes in a hidden field */ ?>
                <form class="form" action="<?= url('/reset-password') ?>" method="post">
                    <?= \PostCard\Core\Csrf::field() ?>
                    <div class="field">
                        <label class="field__label" for="password">Ny adgangskode</label>
                        <div class="password-field">
                            <input class="field__input" id="password" name="password" type="password"
                                autocomplete="new-password" minlength="12" required
                                aria-describedby="password-hint">
                            <button class="password-field__toggle js-password-toggle" type="button" aria-controls="password" hidden>
                                <img class="password-field__icon password-field__eye-closed" src="<?= asset('assets/images/icons/hide-password.svg') ?>" alt="">
                                <img class="password-field__icon password-field__eye-open" src="<?= asset('assets/images/icons/show-password.svg') ?>" alt="">
                                <span class="js-password-toggle-text">Vis</span><span class="visually-hidden"> adgangskode</span>
                            </button>
                        </div>
                        <p class="meta" id="password-hint">Mindst 12 tegn.</p>
                    </div>

                    <div class="field">
                        <label class="field__label" for="password-confirm">Gentag ny adgangskode</label>
                        <div class="password-field">
                            <input class="field__input" id="password-confirm" name="password_confirm" type="password"
                                autocomplete="new-password" minlength="12" required>
                            <button class="password-field__toggle js-password-toggle" type="button" aria-controls="password-confirm" hidden>
                                <img class="password-field__icon password-field__eye-closed" src="<?= asset('assets/images/icons/hide-password.svg') ?>" alt="">
                                <img class="password-field__icon password-field__eye-open" src="<?= asset('assets/images/icons/show-password.svg') ?>" alt="">
                                <span class="js-password-toggle-text">Vis</span><span class="visually-hidden"> adgangskode</span>
                            </button>
                        </div>
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
