<?php
$pageTitle = 'Opret bruger';
$isLoggedIn = false;
require __DIR__ . '/../views/partials/header.php';
?>

<main id="main" class="site-main">
    <div class="container">
        <div class="envelope envelope--still">
            <div class="envelope__back" aria-hidden="true"></div>
            <div class="envelope__letter flow">
                <p class="eyebrow">Ny bruger</p>
                <h1>Opret bruger</h1>
                <p>Du skal bruge en bruger for at blive medlem af en gruppe.</p>

                <!-- A CSRF token is added in Epic 2 -->
                <form class="form" action="register.php" method="post">
                    <div class="field-row">
                        <div class="field">
                            <label class="field__label" for="first-name">Fornavn</label>
                            <input class="field__input" id="first-name" name="first_name" type="text"
                                autocomplete="given-name" maxlength="100" required>
                        </div>
                        <div class="field">
                            <label class="field__label" for="last-name">Efternavn</label>
                            <input class="field__input" id="last-name" name="last_name" type="text"
                                autocomplete="family-name" maxlength="100" required>
                        </div>
                    </div>

                    <div class="field">
                        <label class="field__label" for="username">Brugernavn</label>
                        <input class="field__input" id="username" name="username" type="text"
                            autocomplete="username" maxlength="50" required
                            aria-describedby="username-hint">
                        <p class="meta" id="username-hint">Det navn, andre i dine grupper ser.</p>
                    </div>

                    <div class="field">
                        <label class="field__label" for="email">E-mail</label>
                        <input class="field__input" id="email" name="email" type="email"
                            autocomplete="email" maxlength="255" required>
                    </div>

                    <div class="field">
                        <label class="field__label" for="birthdate">Fødselsdato</label>
                        <input class="field__input" id="birthdate" name="birthdate" type="date"
                            autocomplete="bday" required>
                    </div>

                    <div class="field">
                        <label class="field__label" for="password">Adgangskode</label>
                        <input class="field__input" id="password" name="password" type="password"
                            autocomplete="new-password" minlength="12" required
                            aria-describedby="password-hint">
                        <p class="meta" id="password-hint">Mindst 12 tegn. En sætning er nem at huske.</p>
                    </div>

                    <div class="field">
                        <label class="field__label" for="password-confirm">Gentag adgangskode</label>
                        <input class="field__input" id="password-confirm" name="password_confirm" type="password"
                            autocomplete="new-password" minlength="12" required>
                    </div>

                    <div class="field field--checkbox">
                        <input class="field__checkbox" id="accept-rules" name="accept_rules" type="checkbox" value="1" required>
                        <label for="accept-rules">Jeg har læst og accepterer <a href="rules.php">reglerne</a>.</label>
                    </div>

                    <div class="form__actions">
                        <button class="button button--primary" type="submit">Opret bruger</button>
                        <p>Har du allerede en bruger? <a href="login.php">Log ind</a></p>
                    </div>
                </form>
            </div>
            <div class="envelope__front" aria-hidden="true"></div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../views/partials/footer.php'; ?>
