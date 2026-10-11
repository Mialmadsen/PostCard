<?php
$pageTitle = 'Anmodninger · Familien Nielsen';
$isLoggedIn = true;
$pageScripts = ['confirm'];
require __DIR__ . '/../views/partials/header.php';
?>

<main id="main" class="site-main">
    <div class="container container--text">
        <nav class="breadcrumb" aria-label="Du er her">
            <ol class="breadcrumb__list">
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="home.php">Mine grupper</a></li>
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="group.php?id=1">Familien Nielsen</a></li>
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="requests.php?group=1" aria-current="page">Anmodninger</a></li>
            </ol>
        </nav>

        <header class="page-header">
            <p class="group-label">Familien Nielsen · kun for admins</p>
            <h1>Anmodninger</h1>
            <p>Medlemmer, der vil dele en oplevelse. Du kan godkende den, ændre perioden eller antallet af opslag først, eller afvise den.</p>
        </header>

        <!-- IF there are no pending requests: show only this
        <p class="alert">Der er ingen anmodninger lige nu.</p>
        -->

        <!-- LOOP: experiences in this group with experience_status = 'pending', oldest first -->
        <div class="card-stack">

            <article class="card flow" aria-labelledby="request-1-heading">
                <p class="group-label">Fra Anna Nielsen · sendt <time datetime="2026-10-08">8. okt.</time></p>
                <h2 id="request-1-heading">Efterårsferie i Rom</h2>
                <p>Vi tager til Rom i efterårsferien og vil gerne dele turen med resten af familien.</p>

                <!-- A CSRF token is added in Epic 2.
                     Approve: experience_status = 'approved', with the dates and post_limit below. -->
                <form class="form" id="approve-1" action="requests.php" method="post">
                    <input type="hidden" name="experience_id" value="8">
                    <input type="hidden" name="decision" value="approve">

                    <fieldset class="fieldset">
                        <legend class="field__label fieldset__legend">Periode</legend>
                        <div class="field-row">
                            <div class="field">
                                <label for="start-date-1">Fra</label>
                                <input class="field__input" id="start-date-1" name="start_date" type="date" value="2026-10-17" required>
                            </div>
                            <div class="field">
                                <label for="end-date-1">Til</label>
                                <input class="field__input" id="end-date-1" name="end_date" type="date" value="2026-10-25" required>
                            </div>
                        </div>
                    </fieldset>

                    <div class="field">
                        <label class="field__label" for="post-limit-1">Antal opslag</label>
                        <input class="field__input field__input--short" id="post-limit-1" name="post_limit" type="number"
                            min="1" max="50" value="20" required aria-describedby="post-limit-1-hint">
                        <p class="meta" id="post-limit-1-hint">Gruppens standard er 20. Hvert opslag kan have op til 5 billeder.</p>
                    </div>
                </form>

                <!-- The "Godkend" button belongs to the form above through form="approve-1",
                     so it can stand next to the separate "Afvis" form -->
                <div class="button-row">
                    <button class="button button--primary" type="submit" form="approve-1">Godkend</button>
                    <!-- Reject: experience_status = 'rejected' -->
                    <form class="js-confirm" action="requests.php" method="post"
                        data-confirm="Vil du afvise Efterårsferie i Rom? Anna Nielsen kan se det på sin forside.">
                        <input type="hidden" name="experience_id" value="8">
                        <input type="hidden" name="decision" value="reject">
                        <button class="button button--secondary" type="submit">Afvis</button>
                    </form>
                </div>
            </article>

            <article class="card flow" aria-labelledby="request-2-heading">
                <p class="group-label">Fra Jens Nielsen · sendt <time datetime="2026-10-10">10. okt.</time></p>
                <h2 id="request-2-heading">Fisketur til Bornholm</h2>
                <p>En lang weekend med fiskestænger og røget sild.</p>

                <form class="form" id="approve-2" action="requests.php" method="post">
                    <input type="hidden" name="experience_id" value="9">
                    <input type="hidden" name="decision" value="approve">

                    <fieldset class="fieldset">
                        <legend class="field__label fieldset__legend">Periode</legend>
                        <div class="field-row">
                            <div class="field">
                                <label for="start-date-2">Fra</label>
                                <input class="field__input" id="start-date-2" name="start_date" type="date" value="2026-10-30" required>
                            </div>
                            <div class="field">
                                <label for="end-date-2">Til</label>
                                <input class="field__input" id="end-date-2" name="end_date" type="date" value="2026-11-02" required>
                            </div>
                        </div>
                    </fieldset>

                    <div class="field">
                        <label class="field__label" for="post-limit-2">Antal opslag</label>
                        <input class="field__input field__input--short" id="post-limit-2" name="post_limit" type="number"
                            min="1" max="50" value="20" required aria-describedby="post-limit-2-hint">
                        <p class="meta" id="post-limit-2-hint">Gruppens standard er 20. Hvert opslag kan have op til 5 billeder.</p>
                    </div>
                </form>

                <div class="button-row">
                    <button class="button button--primary" type="submit" form="approve-2">Godkend</button>
                    <form class="js-confirm" action="requests.php" method="post"
                        data-confirm="Vil du afvise Fisketur til Bornholm? Jens Nielsen kan se det på sin forside.">
                        <input type="hidden" name="experience_id" value="9">
                        <input type="hidden" name="decision" value="reject">
                        <button class="button button--secondary" type="submit">Afvis</button>
                    </form>
                </div>
            </article>

        </div>
        <!-- /LOOP -->
    </div>
</main>

<?php require __DIR__ . '/../views/partials/footer.php'; ?>
