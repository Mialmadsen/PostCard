<?php
// The controller shows this page with:
// View::render('admin-login', ['pageTitle' => 'Administration · Log ind', 'isLoggedIn' => false]);
?>

<!-- Login for operators (users.is_operator = 1). The same users table as normal login,
     but the server also checks is_operator, and the session remembers that this is an operator. -->

<main id="main" class="site-main">
    <div class="container container--text">
        <section class="card flow" aria-labelledby="login-heading">
            <p class="eyebrow">Administration</p>
            <h1 id="login-heading">Log ind som operatør</h1>
            <p>Kun for dem, der driver <span class="brand">PostCard</span>. Er du almindelig bruger, så <a href="<?= url('/login') ?>">log ind her</a>.</p>

            <!-- IF login failed (wrong details OR not an operator — the same message, so it does not reveal which):
            <p class="alert alert--error" role="alert">Forkert e-mail eller adgangskode.</p> -->

            <form class="form" action="<?= url('/admin/login') ?>" method="post">
                <?= \PostCard\Core\Csrf::field() ?>
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

                <div class="button-row">
                    <button class="button button--primary" type="submit">Log ind</button>
                </div>
            </form>
        </section>
    </div>
</main>
