<?php
// The controller shows this page with:
// View::render('group-settings', ['pageTitle' => 'Indstillinger · Familien Nielsen', 'isLoggedIn' => true]);
?>

<main id="main" class="site-main">
    <div class="container container--text">
        <nav class="breadcrumb" aria-label="Du er her">
            <ol class="breadcrumb__list">
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="<?= url('/home') ?>">Mine grupper</a></li>
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="<?= url('/group?id=1') ?>">Familien Nielsen</a></li>
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="<?= url('/group-settings?group_id=1') ?>" aria-current="page">Indstillinger</a></li>
            </ol>
        </nav>

        <header class="page-header">
            <p class="group-label">Familien Nielsen · kun for admins</p>
            <h1>Gruppens indstillinger</h1>
        </header>

        <!-- IF saved: <p class="alert" role="status">Indstillingerne er gemt.</p> -->

        <div class="card-stack">
            <section class="card" aria-labelledby="form-heading">
                <h2 class="section-title" id="form-heading">Om gruppen</h2>

                <!--
                     The same fields as create-group.php, filled in from user_groups. -->
                <form class="form" action="<?= url('/group-settings') ?>" method="post">
                    <?= \PostCard\Core\Csrf::field() ?>
                    <input type="hidden" name="group_id" value="1">

                    <div class="field">
                        <label class="field__label" for="name">Gruppens navn</label>
                        <input class="field__input" id="name" name="name" type="text"
                            maxlength="100" required value="Familien Nielsen">
                    </div>

                    <div class="field">
                        <label class="field__label" for="description">Beskrivelse</label>
                        <textarea class="field__input" id="description" name="description" rows="3" maxlength="1000"
                            aria-describedby="description-hint">Rejser, ferier og weekendture med hele familien.</textarea>
                        <p class="meta" id="description-hint">Medlemmerne ser den øverst på gruppens side.</p>
                    </div>

                    <fieldset class="fieldset">
                        <legend class="field__label fieldset__legend">Hvem må dele oplevelser?</legend>
                        <div class="field field--checkbox">
                            <input class="field__checkbox" id="mode-open" name="posting_mode" type="radio" value="open" checked
                                aria-describedby="mode-open-hint">
                            <div>
                                <label for="mode-open">Alle medlemmer</label>
                                <p class="meta" id="mode-open-hint">Medlemmerne kan starte en oplevelse med det samme. Anmodninger, der allerede venter, skal stadig godkendes under Anmodninger.</p>
                            </div>
                        </div>
                        <div class="field field--checkbox">
                            <input class="field__checkbox" id="mode-permission" name="posting_mode" type="radio" value="permission"
                                aria-describedby="mode-permission-hint">
                            <div>
                                <label for="mode-permission">Kun med tilladelse</label>
                                <p class="meta" id="mode-permission-hint">En admin skal godkende hver oplevelse, før medlemmet kan dele opslag. Oplevelser, der allerede er i gang, fortsætter.</p>
                            </div>
                        </div>
                    </fieldset>

                    <div class="field">
                        <label class="field__label" for="post-limit">Antal opslag pr. oplevelse</label>
                        <input class="field__input field__input--short" id="post-limit" name="default_post_limit" type="number"
                            min="1" max="50" value="20" required aria-describedby="post-limit-hint">
                        <p class="meta" id="post-limit-hint">Gælder nye oplevelser. Oplevelser, der allerede er i gang, beholder deres antal.</p>
                    </div>

                    <!-- user_groups.style is not in the form: no page uses it yet -->

                    <div class="button-row">
                        <button class="button button--primary" type="submit">Gem indstillinger</button>
                        <a class="button button--secondary" href="<?= url('/group?id=1') ?>">Annuller</a>
                    </div>
                </form>
            </section>
        </div>
    </div>
</main>
