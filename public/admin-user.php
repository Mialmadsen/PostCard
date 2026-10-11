<?php
$pageTitle = 'Administration · Mia Nielsen';
$isOperator = true;
$pageStyle = 'admin';
$adminPage = 'users';
require __DIR__ . '/../views/partials/header.php';
?>

<main id="main" class="site-main">
    <div class="container">
        <nav class="breadcrumb" aria-label="Du er her">
            <ol class="breadcrumb__list">
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="admin.php">Administration</a></li>
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="admin-users.php">Brugere</a></li>
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="admin-user.php?id=1" aria-current="page">Mia Nielsen</a></li>
            </ol>
        </nav>

        <header class="page-header">
            <p class="eyebrow">Administration · bruger</p>
            <h1>Mia Nielsen</h1>
            <p class="meta">Oprettet <time datetime="2026-09-01">1. sep. 2026</time> · e-mail bekræftet · 3 grupper · 38 opslag</p>
        </header>

        <!-- IF saved: <p class="alert" role="status">Ændringerne er gemt.</p> -->

        <div class="sidebar-layout">
            <?php require __DIR__ . '/../views/partials/admin-nav.php'; ?>

            <div class="flow">
                <section class="card" aria-labelledby="profile-heading">
                    <h2 class="section-title" id="profile-heading">Profil</h2>

                    <!-- A CSRF token is added in Epic 2. The same fields the user has on settings.php -->
                    <form class="form" action="admin-user.php" method="post">
                        <input type="hidden" name="user_id" value="1">
                        <input type="hidden" name="form" value="profile">

                        <div class="field-row">
                            <div class="field">
                                <label class="field__label" for="first-name">Fornavn</label>
                                <input class="field__input" id="first-name" name="first_name" type="text"
                                    maxlength="100" required value="Mia">
                            </div>
                            <div class="field">
                                <label class="field__label" for="last-name">Efternavn</label>
                                <input class="field__input" id="last-name" name="last_name" type="text"
                                    maxlength="100" required value="Nielsen">
                            </div>
                        </div>

                        <div class="field">
                            <label class="field__label" for="username">Brugernavn</label>
                            <input class="field__input" id="username" name="username" type="text"
                                maxlength="50" required value="mianielsen">
                        </div>

                        <div class="field">
                            <label class="field__label" for="email">E-mail</label>
                            <input class="field__input" id="email" name="email" type="email"
                                maxlength="255" required value="mia@example.com">
                        </div>

                        <!-- IF users.profile_image is set: the operator can remove an unsuitable picture
                        <div class="field field--checkbox">
                            <input class="field__checkbox" id="remove-picture" name="remove_picture" type="checkbox" value="1">
                            <label for="remove-picture">Fjern profilbilledet</label>
                        </div> -->

                        <div class="button-row">
                            <button class="button button--primary" type="submit">Gem profil</button>
                        </div>
                    </form>
                </section>

                <section class="card" aria-labelledby="status-heading">
                    <h2 class="section-title" id="status-heading">Status</h2>

                    <!-- users.user_status. Not shown on the operator's own page, so nobody can lock themselves out. -->
                    <form class="form" action="admin-user.php" method="post">
                        <input type="hidden" name="user_id" value="1">
                        <input type="hidden" name="form" value="status">

                        <fieldset class="fieldset">
                            <legend class="visually-hidden">Brugerens status</legend>
                            <div class="field field--checkbox">
                                <input class="field__checkbox" id="status-active" name="user_status" type="radio" value="active" checked
                                    aria-describedby="status-active-hint">
                                <div>
                                    <label for="status-active">Aktiv</label>
                                    <p class="meta" id="status-active-hint">Kan logge ind og bruge sine grupper.</p>
                                </div>
                            </div>
                            <div class="field field--checkbox">
                                <input class="field__checkbox" id="status-blocked" name="user_status" type="radio" value="blocked"
                                    aria-describedby="status-blocked-hint">
                                <div>
                                    <label for="status-blocked">Blokeret</label>
                                    <p class="meta" id="status-blocked-hint">Kan ikke logge ind. Opslag og kommentarer bliver i grupperne. Kan fortrydes.</p>
                                </div>
                            </div>
                            <div class="field field--checkbox">
                                <input class="field__checkbox" id="status-deleted" name="user_status" type="radio" value="deleted"
                                    aria-describedby="status-deleted-hint">
                                <div>
                                    <label for="status-deleted">Slettet</label>
                                    <p class="meta" id="status-deleted-hint">Kan ikke logge ind. Brug det, når brugeren beder om at blive slettet.</p>
                                </div>
                            </div>
                        </fieldset>

                        <div class="button-row">
                            <button class="button button--primary" type="submit">Gem status</button>
                        </div>
                    </form>
                </section>

                <section class="card flow" aria-labelledby="password-heading">
                    <h2 id="password-heading">Adgangskode</h2>
                    <p>Operatører kan ikke se eller vælge en brugers adgangskode. Send i stedet et link, så brugeren selv kan vælge en ny.</p>

                    <!-- A CSRF token is added in Epic 2. Makes a reset_password token (user_tokens) and e-mails the link. -->
                    <form action="admin-user.php" method="post">
                        <input type="hidden" name="user_id" value="1">
                        <input type="hidden" name="form" value="reset-password">
                        <button class="button button--secondary" type="submit">Send link til ny adgangskode</button>
                    </form>
                </section>

                <section class="card" aria-labelledby="groups-heading">
                    <h2 class="section-title" id="groups-heading">Grupper</h2>

                    <!-- LOOP: group_members for this user, with member_status -->
                    <ul class="divided-list">
                        <li>
                            <p>Familien Nielsen</p>
                            <p class="meta">Admin · medlem siden <time datetime="2026-09-01">1. sep. 2026</time> · 31 opslag</p>
                        </li>
                        <li>
                            <p>Klasse 3.B</p>
                            <p class="meta">Medlem · medlem siden <time datetime="2026-09-03">3. sep. 2026</time> · 4 opslag</p>
                        </li>
                        <li>
                            <p>Løbeklubben</p>
                            <p class="meta">Medlem · medlem siden <time datetime="2026-09-04">4. sep. 2026</time> · 3 opslag</p>
                        </li>
                    </ul>
                    <!-- /LOOP -->

                    <p><a href="admin-posts.php?user=1">Se brugerens opslag</a></p>
                </section>
            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../views/partials/footer.php'; ?>
