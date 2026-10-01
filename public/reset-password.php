<?php
$pageTitle = 'Ny adgangskode';
$isLoggedIn = false;
require __DIR__ . '/../views/partials/header.php';
?>

<main id="main" class="site-main">
    <div class="container">
        <div class="envelope envelope--still">
            <div class="envelope__back" aria-hidden="true"></div>
            <div class="envelope__letter flow">
                <p class="eyebrow">Ny adgangskode</p>
                <h1>Vælg en ny adgangskode</h1>

                <!-- IF the link is expired or used: <p class="alert alert--error" role="alert">Linket er udløbet. <a href="forgot-password.php">Bed om et nyt link</a>.</p> -->

                <!-- A CSRF token and the reset token (hidden field) are added in Epic 2 -->
                <form class="form" action="reset-password.php" method="post">
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

<?php require __DIR__ . '/../views/partials/footer.php'; ?>
