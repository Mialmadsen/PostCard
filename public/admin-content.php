<?php
$pageTitle = 'Administration · Indhold';
$isOperator = true;
$pageStyle = 'admin';
$adminPage = 'content';
require __DIR__ . '/../views/partials/header.php';
?>

<!-- The texts on the public pages (site_content). One form per text, so saving one
     does not touch the others. PHP escapes the text when it shows it, and turns
     an empty line into a new paragraph. -->

<main id="main" class="site-main">
    <div class="container">
        <p class="eyebrow">Administration</p>
        <h1>Indhold</h1>
        <p>Teksterne på de offentlige sider. En tom linje giver et nyt afsnit.</p>

        <!-- IF saved: <p class="alert" role="status">Teksten er gemt.</p> -->

        <div class="sidebar-layout">
            <?php require __DIR__ . '/../views/partials/admin-nav.php'; ?>

            <div class="flow">

                <!-- LOOP: site_content rows -->
                <section class="card" aria-labelledby="welcome-heading">
                    <h2 class="section-title" id="welcome-heading">Forsiden: velkomst</h2>

                    <!-- A CSRF token is added in Epic 2 -->
                    <form class="form" action="admin-content.php" method="post">
                        <input type="hidden" name="content_key" value="welcome">

                        <div class="field">
                            <label class="field__label" for="welcome-title">Overskrift</label>
                            <input class="field__input" id="welcome-title" name="title" type="text" maxlength="150" required
                                value="Del din oplevelse – ikke hele dit liv">
                        </div>
                        <div class="field">
                            <label class="field__label" for="welcome-body">Tekst</label>
                            <textarea class="field__input" id="welcome-body" name="body" rows="3" required>Et privat sted at dele rejseminder med de mennesker, der betyder noget.</textarea>
                        </div>

                        <p class="meta">Sidst ændret <time datetime="2026-09-20">20. sep. 2026</time> af Mia Nielsen · <a href="index.php">Se siden</a></p>

                        <div class="button-row">
                            <button class="button button--primary" type="submit">Gem velkomst</button>
                        </div>
                    </form>
                </section>

                <section class="card" aria-labelledby="rules-heading">
                    <h2 class="section-title" id="rules-heading">Regler</h2>

                    <form class="form" action="admin-content.php" method="post">
                        <input type="hidden" name="content_key" value="rules">

                        <div class="field">
                            <label class="field__label" for="rules-title">Overskrift</label>
                            <input class="field__input" id="rules-title" name="title" type="text" maxlength="150" required
                                value="Regler og retningslinjer">
                        </div>
                        <div class="field">
                            <label class="field__label" for="rules-body">Tekst</label>
                            <textarea class="field__input" id="rules-body" name="body" rows="10" required>PostCard er et privat sted for grupper, der kender hinanden. For at det skal føles trygt for alle, gælder disse regler:

Del kun billeder, du selv har taget eller har lov til at dele.

Spørg, før du deler billeder af andre – især af børn.

Det, der deles i en gruppe, bliver i gruppen. Del det ikke videre uden for PostCard.

Intet krænkende, voldeligt eller ulovligt indhold.

Skriv venligt og respektfuldt i kommentarer.

Gruppens admin kan fjerne opslag og medlemmer, der bryder reglerne. PostCard kan blokere brugere.</textarea>
                        </div>

                        <p class="meta">Sidst ændret <time datetime="2026-09-21">21. sep. 2026</time> af Mia Nielsen · <a href="rules.php">Se siden</a></p>

                        <div class="button-row">
                            <button class="button button--primary" type="submit">Gem regler</button>
                        </div>
                    </form>
                </section>

                <section class="card" aria-labelledby="privacy-heading">
                    <h2 class="section-title" id="privacy-heading">Regler: dine data</h2>

                    <form class="form" action="admin-content.php" method="post">
                        <input type="hidden" name="content_key" value="privacy">

                        <div class="field">
                            <label class="field__label" for="privacy-title">Overskrift</label>
                            <input class="field__input" id="privacy-title" name="title" type="text" maxlength="150" required
                                value="Dine data">
                        </div>
                        <div class="field">
                            <label class="field__label" for="privacy-body">Tekst</label>
                            <textarea class="field__input" id="privacy-body" name="body" rows="3" required>Du kan altid slette din bruger. Så bliver dine oplysninger og dine opslag slettet.</textarea>
                        </div>

                        <p class="meta">Sidst ændret <time datetime="2026-09-21">21. sep. 2026</time> af Mia Nielsen · <a href="rules.php">Se siden</a></p>

                        <div class="button-row">
                            <button class="button button--primary" type="submit">Gem dine data</button>
                        </div>
                    </form>
                </section>

                <section class="card" aria-labelledby="contact-heading">
                    <h2 class="section-title" id="contact-heading">Kontakt</h2>

                    <form class="form" action="admin-content.php" method="post">
                        <input type="hidden" name="content_key" value="contact">

                        <div class="field">
                            <label class="field__label" for="contact-title">Overskrift</label>
                            <input class="field__input" id="contact-title" name="title" type="text" maxlength="150" required
                                value="Kontakt">
                        </div>
                        <div class="field">
                            <label class="field__label" for="contact-body">Tekst</label>
                            <textarea class="field__input" id="contact-body" name="body" rows="6" required>Har du spørgsmål, eller vil du anmelde indhold, der bryder reglerne? Skriv til os, så svarer vi inden for to hverdage.

E-mail: kontakt@postcard.example

Adresse: PostCard, Eksempelvej 1, 6700 Esbjerg</textarea>
                        </div>

                        <p class="meta">Sidst ændret <time datetime="2026-09-21">21. sep. 2026</time> af Mia Nielsen · <a href="contact.php">Se siden</a></p>

                        <div class="button-row">
                            <button class="button button--primary" type="submit">Gem kontakt</button>
                        </div>
                    </form>
                </section>
                <!-- /LOOP -->

            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../views/partials/footer.php'; ?>
