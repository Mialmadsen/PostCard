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

                <!-- IF the page was opened from an invitation (/login?invite=…) and the link is valid:
                <p class="alert">Når du er logget ind, bliver du medlem af <strong>Familien Nielsen</strong>.</p>
                -->

                <form class="form" action="<?= url('/login') ?>" method="post">
                    <?= \PostCard\Core\Csrf::field() ?>
                    <!-- IF invitation: <input type="hidden" name="invite" value="TOKEN"> so the token survives the form -->
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
                        <p>Ingen bruger endnu? <a href="<?= url('/register') ?>">Opret bruger</a></p>
                    </div>
                </form>
            </div>
            <div class="envelope__front" aria-hidden="true"></div>
        </div>
    </div>
</main>