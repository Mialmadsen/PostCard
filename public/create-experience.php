<?php
$pageTitle = 'Ny oplevelse';
$isLoggedIn = true;
require __DIR__ . '/../views/partials/header.php';
?>

<!-- One form, two posting modes (user_groups.posting_mode):
     - 'permission' (shown here): the experience is sent to the group admin as a request
       and saved with experience_status = 'pending'.
     - 'open': the experience starts right away, experience_status = 'approved'.
       The texts that differ are in the IF comments below. -->

<main id="main" class="site-main">
    <div class="container">
        <nav class="breadcrumb" aria-label="Du er her">
            <ol class="breadcrumb__list">
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="home.php">Mine grupper</a></li>
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="group.php?id=1">Familien Nielsen</a></li>
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="create-experience.php?group=1" aria-current="page">Ny oplevelse</a></li>
            </ol>
        </nav>

        <header class="page-header">
            <p class="group-label">Familien Nielsen</p>
            <h1>Ny oplevelse</h1>
            <p>I denne gruppe skal gruppens admin godkende din oplevelse, før du kan dele opslag.</p>
            <!-- IF posting_mode = 'open':
            <p>Start en oplevelse, og del dine opslag med gruppen, mens den står på.</p>
            -->
        </header>

        <div class="sidebar-layout">

            <!-- Left (first on phones): where the experience goes and who decides -->
            <aside class="card flow" aria-labelledby="address-heading">
                <h2 id="address-heading">Til</h2>
                <dl class="address">
                    <div>
                        <dt class="group-label">Gruppe</dt>
                        <dd class="address__value">Familien Nielsen</dd>
                    </div>
                    <!-- IF posting_mode = 'permission' -->
                    <div>
                        <dt class="group-label">Godkendes af</dt>
                        <dd class="address__value">Peter Nielsen</dd>
                    </div>
                    <!-- /IF -->
                </dl>
            </aside>

            <section class="card" aria-labelledby="form-heading">
                <h2 class="section-title" id="form-heading">Din oplevelse</h2>

                <!-- A CSRF token is added in Epic 2 -->
                <form class="form" action="create-experience.php" method="post">
                    <input type="hidden" name="group_id" value="1">

                    <!-- IF there is no group in the URL (the link on the front page):
                         the user picks one of their own groups instead of the hidden field above.
                    <div class="field">
                        <label class="field__label" for="group">Gruppe</label>
                        <select class="field__input" id="group" name="group_id" required>
                            <option value="">Vælg en gruppe</option>
                            LOOP: the user's groups
                            <option value="1">Familien Nielsen</option>
                        </select>
                    </div>
                    -->

                    <div class="field">
                        <label class="field__label" for="name">Navn på oplevelsen</label>
                        <input class="field__input" id="name" name="name" type="text"
                            maxlength="100" required aria-describedby="name-hint">
                        <p class="meta" id="name-hint">Fx Sommer i Norge eller Studietur til Berlin</p>
                    </div>

                    <div class="field">
                        <label class="field__label" for="description">Hvad handler den om?</label>
                        <textarea class="field__input" id="description" name="description" rows="4" maxlength="1000"
                            aria-describedby="description-hint"></textarea>
                        <p class="meta" id="description-hint">Gruppen ser teksten øverst på oplevelsens side. Du kan springe den over.</p>
                    </div>

                    <!-- The server checks that the end date is not before the start date -->
                    <fieldset class="fieldset" aria-describedby="period-hint">
                        <legend class="field__label fieldset__legend">Periode</legend>
                        <div class="field-row">
                            <div class="field">
                                <label for="start-date">Fra</label>
                                <input class="field__input" id="start-date" name="start_date" type="date" required>
                            </div>
                            <div class="field">
                                <label for="end-date">Til</label>
                                <input class="field__input" id="end-date" name="end_date" type="date" required>
                            </div>
                        </div>
                        <p class="meta" id="period-hint">Du kan dele opslag fra den første til den sidste dag.</p>
                    </fieldset>

                    <!-- Answers "What happens when I send this?" before the button -->
                    <p class="alert">Din anmodning bliver sendt til <strong>Peter Nielsen</strong>, som er admin i Familien Nielsen. Når den er godkendt, kan du dele op til 20 opslag med op til 5 billeder hver. Du kan følge status på din forside.</p>
                    <!-- IF posting_mode = 'open':
                    <p class="alert">Oplevelsen bliver oprettet med det samme i <strong>Familien Nielsen</strong>, og du kan dele op til 20 opslag med op til 5 billeder hver fra startdatoen.</p>
                    -->

                    <div class="button-row">
                        <button class="button button--primary" type="submit">Send anmodning</button>
                        <!-- IF posting_mode = 'open': the button text is "Opret oplevelse" -->
                        <a class="button button--secondary" href="group.php?id=1">Annuller</a>
                    </div>
                </form>
            </section>

        </div>
    </div>
</main>

<?php require __DIR__ . '/../views/partials/footer.php'; ?>
