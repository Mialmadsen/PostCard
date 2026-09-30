<?php
$pageTitle = 'Log ind';
$isLoggedIn = false;
require __DIR__ . '/../views/partials/header.php';
?>

<main id="main" class="site-main">
    <div class="container">
        <div class="envelope envelope--still">
            <div class="envelope__back" aria-hidden="true"></div>
            <div class="envelope__letter flow">
                <p class="eyebrow">Velkommen tilbage</p>
                <h1>Log ind</h1>

                <!-- IF login failed: <p class="alert alert--error" role="alert">Forkert e-mail eller adgangskode.</p> -->

                <!-- A CSRF token is added in Epic 2 -->
                <form class="form" action="login.php" method="post">
                    <div class="field">
                        <label class="field__label" for="email">E-mail</label>
                        <input class="field__input" id="email" name="email" type="email"
                            autocomplete="email" required>
                    </div>

                    <div class="field">
                        <label class="field__label" for="password">Adgangskode</label>
                        <input class="field__input" id="password" name="password" type="password"
                            autocomplete="current-password" required>
                    </div>

                    <div class="form__actions">
                        <button class="button button--primary" type="submit">Log ind</button>
                        <a href="forgot-password.php">Glemt adgangskode?</a>
                        <p>Ingen bruger endnu? <a href="register.php">Opret bruger</a></p>
                    </div>
                </form>
            </div>
            <div class="envelope__front" aria-hidden="true"></div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../views/partials/footer.php'; ?>
