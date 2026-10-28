<?php
/** @var string $invite the invitation token, or '' when the page was not opened from an invitation */
?>

<main id="main" class="site-main">
    <div class="container">
        <div class="envelope envelope--still">
            <div class="envelope__back" aria-hidden="true"></div>
            <div class="envelope__letter flow">
                <p class="eyebrow">Ny bruger</p>
                <h1>Opret bruger</h1>
                <p>Du skal bruge en bruger for at blive medlem af en gruppe.</p>

                <?php if ($errors !== []): ?>
                <p class="alert alert--error" role="alert">Noget mangler eller er forkert. Se de markerede felter
                    nedenfor.</p>
                <?php endif; ?>

                <?php if ($invite !== ''): ?>
                    <p class="alert">Når du har oprettet din bruger, kommer du tilbage til din invitation.</p>
                <?php endif; ?>

                <form class="form" action="<?= url('/register') ?>" method="post">
                    <?= \PostCard\Core\Csrf::field() ?>
                    <?php if ($invite !== ''): ?>
                        <!-- Carries the invitation token through the form, so the user can be sent back to it -->
                        <input type="hidden" name="invite" value="<?= e($invite) ?>">
                    <?php endif; ?>
                    <div class="field-row">
                        <div class="field">
                            <label class="field__label" for="first-name">Fornavn</label>
                            <input class="field__input" id="first-name" name="first_name" type="text"
                                autocomplete="given-name" maxlength="100" required
                                value="<?= e($old['first_name'] ?? '') ?>"
                                <?php if (isset($errors['first_name'])): ?>aria-invalid="true" aria-describedby="first-name-error"<?php endif; ?>>
                            <?php if (isset($errors['first_name'])): ?>
                                <p class="field__error" id="first-name-error"><?= e($errors['first_name']) ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="field">
                            <label class="field__label" for="last-name">Efternavn</label>
                            <input class="field__input" id="last-name" name="last_name" type="text"
                                autocomplete="family-name" maxlength="100" required
                                value="<?= e($old['last_name'] ?? '') ?>"
                                <?php if (isset($errors['last_name'])): ?>aria-invalid="true" aria-describedby="last-name-error"<?php endif; ?>>
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
                            <?php if (isset($errors['username'])): ?>aria-invalid="true" aria-describedby="username-hint username-error"<?php else: ?>aria-describedby="username-hint"<?php endif; ?>>
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
                            <?php if (isset($errors['email'])): ?>aria-invalid="true" aria-describedby="email-error"<?php endif; ?>>
                        <?php if (isset($errors['email'])): ?>
                            <p class="field__error" id="email-error"><?= e($errors['email']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="field">
                        <label class="field__label" for="birthdate">Fødselsdato</label>
                        <input class="field__input" id="birthdate" name="birthdate" type="date"
                            autocomplete="bday" required
                            value="<?= e($old['birthdate'] ?? '') ?>"
                            <?php if (isset($errors['birthdate'])): ?>aria-invalid="true" aria-describedby="birthdate-error"<?php endif; ?>>
                        <?php if (isset($errors['birthdate'])): ?>
                            <p class="field__error" id="birthdate-error"><?= e($errors['birthdate']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="field">
                        <label class="field__label" for="password">Adgangskode</label>
                        <input class="field__input" id="password" name="password" type="password"
                            autocomplete="new-password" minlength="12" required
                            <?php if (isset($errors['password'])): ?>aria-invalid="true" aria-describedby="password-hint password-error"<?php else: ?>aria-describedby="password-hint"<?php endif; ?>>
                        <p class="meta" id="password-hint">Mindst 12 tegn. En sætning er nem at huske.</p>
                        <?php if (isset($errors['password'])): ?>
                            <p class="field__error" id="password-error"><?= e($errors['password']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="field">
                        <label class="field__label" for="password-confirm">Gentag adgangskode</label>
                        <input class="field__input" id="password-confirm" name="password_confirm" type="password"
                            autocomplete="new-password" minlength="12" required
                            <?php if (isset($errors['password_confirm'])): ?>aria-invalid="true" aria-describedby="password-confirm-error"<?php endif; ?>>
                        <?php if (isset($errors['password_confirm'])): ?>
                            <p class="field__error" id="password-confirm-error"><?= e($errors['password_confirm']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="field field--checkbox">
                        <input class="field__checkbox" id="accept-rules" name="accept_rules" type="checkbox" value="1"
                            required
                            <?php if (isset($errors['accept_rules'])): ?>aria-invalid="true" aria-describedby="accept-rules-error"<?php endif; ?>>
                        <label for="accept-rules">Jeg har læst og accepterer <a href="<?= url('/rules') ?>">reglerne</a>.</label>
                        <?php if (isset($errors['accept_rules'])): ?>
                            <p class="field__error" id="accept-rules-error"><?= e($errors['accept_rules']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="form__actions">
                        <button class="button button--primary" type="submit">Opret bruger</button>
                        <p>Har du allerede en bruger? <a href="<?= url($invite !== '' ? '/login?invite=' . urlencode($invite) : '/login') ?>">Log ind</a></p>
                    </div>
                </form>
            </div>
            <div class="envelope__front" aria-hidden="true"></div>
        </div>
    </div>
</main>