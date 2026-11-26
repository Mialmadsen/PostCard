<main id="main" class="site-main">
    <div class="container">
        <div class="envelope envelope--still">
            <div class="envelope__back" aria-hidden="true"></div>
            <div class="envelope__letter flow">
                <p class="eyebrow">Ny adgangskode</p>
                <h1>Vælg en ny adgangskode</h1>

                <?php if (!$isValid): ?>

                    <p class="alert alert--error" role="alert">
                        Linket er ugyldigt, brugt eller udløbet.
                        <a href="<?= url('/forgot-password') ?>">Bed om et nyt link</a>.
                    </p>

                    <a href="<?= url('/login') ?>">Tilbage til log ind</a>

                <?php else: ?>

                    <?php if ($errors !== []): ?>
                        <p class="alert alert--error" role="alert">
                            Noget mangler eller er forkert. Se de markerede felter nedenfor.
                        </p>
                    <?php endif; ?>

                    <form class="form" action="<?= url('/reset-password') ?>" method="post">
                        <?= \PostCard\Core\Csrf::field() ?>

                        <?php // Bevarer reset-tokenet, når formularen indsendes. ?>
                        <input type="hidden" name="token" value="<?= e($token) ?>">

                        <div class="field">
                            <label class="field__label" for="password">Ny adgangskode</label>
                            <div class="password-field">
                                <input class="field__input" id="password" name="password" type="password"
                                    autocomplete="new-password" minlength="12" required
                                    <?php if (isset($errors['password'])): ?>aria-invalid="true" aria-describedby="password-hint password-error"<?php else: ?>aria-describedby="password-hint"<?php endif; ?>>
                                <button class="password-field__toggle js-password-toggle" type="button" aria-controls="password" hidden>
                                    <img class="password-field__icon password-field__eye-closed" src="<?= asset('assets/images/icons/hide-password.svg') ?>" alt="">
                                    <img class="password-field__icon password-field__eye-open" src="<?= asset('assets/images/icons/show-password.svg') ?>" alt="">
                                    <span class="js-password-toggle-text">Vis</span><span class="visually-hidden"> adgangskode</span>
                                </button>
                            </div>
                            <p class="meta" id="password-hint">Mindst 12 tegn.</p>

                            <?php if (isset($errors['password'])): ?>
                                <p class="field__error" id="password-error"><?= e($errors['password']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="field">
                            <label class="field__label" for="password-confirm">Gentag ny adgangskode</label>
                            <div class="password-field">
                                <input class="field__input" id="password-confirm" name="password_confirm" type="password"
                                    autocomplete="new-password" minlength="12" required
                                    <?php if (isset($errors['password_confirm'])): ?>aria-invalid="true" aria-describedby="password-confirm-error"<?php endif; ?>>
                                <button class="password-field__toggle js-password-toggle" type="button" aria-controls="password-confirm" hidden>
                                    <img class="password-field__icon password-field__eye-closed" src="<?= asset('assets/images/icons/hide-password.svg') ?>" alt="">
                                    <img class="password-field__icon password-field__eye-open" src="<?= asset('assets/images/icons/show-password.svg') ?>" alt="">
                                    <span class="js-password-toggle-text">Vis</span><span class="visually-hidden"> adgangskode</span>
                                </button>
                            </div>

                            <?php if (isset($errors['password_confirm'])): ?>
                                <p class="field__error" id="password-confirm-error"><?= e($errors['password_confirm']) ?></p>
                            <?php endif; ?>
                        </div>

                        <div class="form__actions">
                            <button class="button button--primary" type="submit">Gem ny adgangskode</button>
                        </div>
                    </form>

                <?php endif; ?>
            </div>
            <div class="envelope__front" aria-hidden="true"></div>
        </div>
    </div>
</main>