<?php
/** @var string $invite the invitation token, or '' when the page was not opened from an invitation */
?>

<main id="main" class="site-main">
    <div class="container">
        <div class="envelope envelope--still">
            <div class="envelope__back" aria-hidden="true"></div>
            <div class="envelope__letter flow">
                <p class="eyebrow">Velkommen tilbage</p>
                <h1>Log ind</h1>

                <?php if (isset($error)): ?>
                    <p class="alert alert--error" role="alert"><?= e($error) ?></p>
                <?php endif; ?>

                <?php if ($invite !== ''): ?>
                    <p class="alert">Når du er logget ind, kommer du tilbage til din invitation.</p>
                <?php endif; ?>

                <form class="form" action="<?= url('/login') ?>" method="post">
                    <?= \PostCard\Core\Csrf::field() ?>
                    <?php if ($invite !== ''): ?>
                        <!-- Carries the invitation token through the form, so the user can be sent back to it -->
                        <input type="hidden" name="invite" value="<?= e($invite) ?>">
                    <?php endif; ?>
                    <div class="field">
                        <label class="field__label" for="email">E-mail</label>
                        <input class="field__input" id="email" name="email" type="email" autocomplete="email"
                            value="<?= e($email ?? '') ?>" required>
                    </div>

                    <div class="field">
                        <label class="field__label" for="password">Adgangskode</label>
                        <input class="field__input" id="password" name="password" type="password"
                            autocomplete="current-password" required>
                    </div>

                    <div class="form__actions">
                        <button class="button button--primary" type="submit">Log ind</button>
                        <a href="<?= url('/forgot-password') ?>">Glemt adgangskode?</a>
                        <p>Ingen bruger endnu? <a href="<?= url($invite !== '' ? '/register?invite=' . urlencode($invite) : '/register') ?>">Opret bruger</a></p>
                    </div>
                </form>
            </div>
            <div class="envelope__front" aria-hidden="true"></div>
        </div>
    </div>
</main>