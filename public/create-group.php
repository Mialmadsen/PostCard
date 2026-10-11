<?php
$pageTitle = 'Ny gruppe';
$isLoggedIn = true;
require __DIR__ . '/../views/partials/header.php';
?>

<main id="main" class="site-main">
    <div class="container container--text">
        <nav class="breadcrumb" aria-label="Du er her">
            <ol class="breadcrumb__list">
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="home.php">Mine grupper</a></li>
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="create-group.php" aria-current="page">Ny gruppe</a></li>
            </ol>
        </nav>

        <header class="page-header">
            <p class="eyebrow">Ny gruppe</p>
            <h1>Opret en gruppe</h1>
            <p>En gruppe er et privat album for folk, du kender. Kun medlemmerne kan se, hvad der bliver delt.</p>
        </header>

        <div class="card-stack">
            <section class="card" aria-labelledby="form-heading">
                <h2 class="section-title" id="form-heading">Om gruppen</h2>

                <!-- A CSRF token is added in Epic 2.
                     The same fields are on group-settings.php; later both pages share one form partial. -->
                <form class="form" action="create-group.php" method="post">
                    <div class="field">
                        <label class="field__label" for="name">Gruppens navn</label>
                        <input class="field__input" id="name" name="name" type="text"
                            maxlength="100" required aria-describedby="name-hint">
                        <p class="meta" id="name-hint">Fx Familien Nielsen, Klasse 3.B eller Løbeklubben</p>
                    </div>

                    <div class="field">
                        <label class="field__label" for="description">Beskrivelse</label>
                        <textarea class="field__input" id="description" name="description" rows="3" maxlength="1000"
                            aria-describedby="description-hint"></textarea>
                        <p class="meta" id="description-hint">Medlemmerne ser den øverst på gruppens side. Du kan springe den over.</p>
                    </div>

                    <!-- user_groups.posting_mode -->
                    <fieldset class="fieldset">
                        <legend class="field__label fieldset__legend">Hvem må dele oplevelser?</legend>
                        <div class="field field--checkbox">
                            <input class="field__checkbox" id="mode-open" name="posting_mode" type="radio" value="open" checked
                                aria-describedby="mode-open-hint">
                            <div>
                                <label for="mode-open">Alle medlemmer</label>
                                <p class="meta" id="mode-open-hint">Medlemmerne kan starte en oplevelse med det samme.</p>
                            </div>
                        </div>
                        <div class="field field--checkbox">
                            <input class="field__checkbox" id="mode-permission" name="posting_mode" type="radio" value="permission"
                                aria-describedby="mode-permission-hint">
                            <div>
                                <label for="mode-permission">Kun med tilladelse</label>
                                <p class="meta" id="mode-permission-hint">En admin skal godkende hver oplevelse, før medlemmet kan dele opslag.</p>
                            </div>
                        </div>
                    </fieldset>

                    <!-- user_groups.default_post_limit -->
                    <div class="field">
                        <label class="field__label" for="post-limit">Antal opslag pr. oplevelse</label>
                        <input class="field__input field__input--short" id="post-limit" name="default_post_limit" type="number"
                            min="1" max="50" value="20" required aria-describedby="post-limit-hint">
                        <p class="meta" id="post-limit-hint">Gælder hver ny oplevelse. Kræver gruppen tilladelse, kan du ændre tallet, når du godkender en oplevelse.</p>
                    </div>

                    <!-- user_groups.style is not in the form: no page uses it yet, so PHP saves 'postcard' -->

                    <!-- Answers "What happens when I create this?" before the button -->
                    <p class="alert">Du bliver <strong>admin</strong> i den nye gruppe. Bagefter kan du invitere medlemmer med et link.</p>

                    <div class="button-row">
                        <button class="button button--primary" type="submit">Opret gruppe</button>
                        <a class="button button--secondary" href="home.php">Annuller</a>
                    </div>
                </form>
            </section>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../views/partials/footer.php'; ?>
