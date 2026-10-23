<?php
// The controller shows this page with:
// View::render('join', ['pageTitle' => 'Invitation', 'isLoggedIn' => false]);
?>

<!-- The page an invitation link opens: /join?token=…
     The server hashes the token and looks it up in invitations.token_hash.
     Four states:
     1. Not logged in (shown here): create a user or log in first; the token is passed on.
     2. Logged in: one button that makes the user a member (see the IF below).
     3. Logged in and already a member: a link to the group.
     4. The link is unknown, used (used_at) or expired (expires_at): only a message, nothing about the group. -->

<main id="main" class="site-main">
    <div class="container">
        <div class="envelope envelope--still">
            <div class="envelope__back" aria-hidden="true"></div>
            <div class="envelope__letter flow">
                <!-- IF the link cannot be used: show ONLY this letter content. Never the group's name,
                     so an old or guessed link tells nothing about the group.
                <p class="eyebrow">Invitation</p>
                <h1>Linket virker ikke</h1>
                <p>Det er måske brugt eller udløbet. Bed den, der inviterede dig, om et nyt link.</p>
                <p><a href="<?= url('/') ?>">Til forsiden</a></p>
                ELSE: -->

                <!-- IF the logged-in user is already a member: show only
                <p>Du er allerede medlem af Familien Nielsen.</p>
                <p><a class="button button--primary" href="<?= url('/group?id=1') ?>">Gå til gruppen</a></p> -->

                <p class="eyebrow">Invitation</p>
                <h1>Du er inviteret til Familien Nielsen</h1>

                <p><strong>Mia Nielsen</strong> har inviteret dig til gruppen.</p>
                <p>Rejser, ferier og weekendture med hele familien.</p>
                <p class="meta">13 medlemmer</p>

                <!-- Answers "Who can see this?" before the user joins -->
                <p class="alert">Det, du deler i gruppen, kan kun gruppens medlemmer se. Andre grupper ser det aldrig.</p>

                <div class="form__actions">
                    <a class="button button--primary" href="<?= url('/register?invite=TOKEN') ?>">Opret bruger og bliv medlem</a>
                    <p>Har du allerede en bruger? <a href="<?= url('/login?invite=TOKEN') ?>">Log ind</a></p>
                </div>

                <!-- IF logged in: instead of the two links above
                <form class="form__actions" action="<?= url('/join') ?>" method="post">
                    <?= \PostCard\Core\Csrf::field() ?>
                    <input type="hidden" name="token" value="TOKEN">
                    <button class="button button--primary" type="submit">Bliv medlem af Familien Nielsen</button>
                </form>
                -->
                <!-- /IF (the link can be used) -->
            </div>
            <div class="envelope__front" aria-hidden="true"></div>
        </div>
    </div>
</main>
