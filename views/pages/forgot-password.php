<?php
// The controller shows this page with:
// View::render('forgot-password', ['pageTitle' => 'Glemt adgangskode', 'isLoggedIn' => false]);
?>

<main id="main" class="site-main">
    <div class="container">
        <div class="envelope envelope--still">
            <div class="envelope__back" aria-hidden="true"></div>
            <div class="envelope__letter flow">
                <p class="eyebrow">Glemt adgangskode</p>
                <h1>Nulstil adgangskode</h1>
                <p>Skriv din e-mail, så sender vi dig et link til at vælge en ny adgangskode.</p>

                <!-- AFTER submit, always the same message (never reveal if the e-mail exists):
                <p class="alert" role="status">Hvis e-mailen findes hos os, har vi sendt et link. Linket virker i 1 time.</p> -->

                <form class="form" action="<?= url('/forgot-password') ?>" method="post">
                    <?= \PostCard\Core\Csrf::field() ?>
                    <div class="field">
                        <label class="field__label" for="email">E-mail</label>
                        <input class="field__input" id="email" name="email" type="email"
                            autocomplete="email" required>
                    </div>

                    <div class="form__actions">
                        <button class="button button--primary" type="submit">Send link</button>
                        <a href="<?= url('/login') ?>">Tilbage til log ind</a>
                    </div>
                </form>
            </div>
            <div class="envelope__front" aria-hidden="true"></div>
        </div>
    </div>
</main>
