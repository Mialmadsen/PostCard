<main id="main" class="site-main">
    <div class="container container--text">
        <nav class="breadcrumb" aria-label="Du er her">
            <ol class="breadcrumb__list">
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="<?= url('/home') ?>">Mine grupper</a>
                </li>
                <li class="breadcrumb__item"><a class="breadcrumb__link"
                        href="<?= url('/group?id=' . $group['group_id']) ?>"><?= e($group['name']) ?></a></li>
                <li class="breadcrumb__item"><a class="breadcrumb__link"
                        href="<?= url('/invite?group_id=' . $group['group_id']) ?>" aria-current="page">Inviter</a></li>
            </ol>
        </nav>

        <header class="page-header">
            <p class="group-label"><?= e($group['name']) ?> · kun for admins</p>
            <h1>Inviter nye medlemmer</h1>
            <p>Lav et link og send det til én person, fx på sms eller mail. Linket virker én gang og udløber efter 7
                dage.</p>
        </header>

        <div class="card-stack">
            <section class="card flow" aria-labelledby="new-heading">
                <h2 id="new-heading">Nyt invitationslink</h2>

                <!--
                     The server makes a random token, saves only its hash (invitations.token_hash)
                     and shows the full link once, right after this form is sent. -->
                <form action="<?= url('/invite') ?>" method="post">
                    <?= \PostCard\Core\Csrf::field() ?>
                    <input type="hidden" name="group_id" value="<?= e($group['group_id']) ?>">
                    <button class="button button--primary" type="submit">Lav et link</button>
                </form>

                <?php if ($inviteLink !== null): ?>
                    <div class="alert flow">
                        <p><strong>Dit link er klar.</strong> Kopiér det nu – du kan ikke se det igen.</p>
                        <div class="copy-link">
                            <label class="visually-hidden" for="invite-link">Invitationslink</label>
                            <input class="field__input copy-link__input js-copy-input" id="invite-link" type="text" readonly
                                value="<?= e($inviteLink) ?>">
                            <button class="button button--secondary js-copy-button" type="button">Kopiér link</button>
                        </div>
                        <!-- Filled by copy-link.js: "Linket er kopieret." -->
                        <p class="meta js-copy-status" role="status"></p>
                    </div>
                <?php endif; ?>
            </section>

            <section class="card" aria-labelledby="sent-heading">
                <h2 class="section-title" id="sent-heading">Dine links</h2>

                <!-- LOOP: invitations for this group, newest first (invitations.created_at).
                     Status: used_at set = "Brugt", expires_at passed = "Udløbet", else "Venter".
                     Only a waiting link has "Annuller". -->
                <ul class="divided-list">
                    <li class="flow">
                        <p>Lavet <time datetime="2026-10-11">11. okt. 2026</time></p>
                        <p class="meta">Venter · udløber <time datetime="2026-10-18">18. okt.</time></p>
                        <!-- Sets expires_at to now, so the link stops working
                             (it is then shown as "Udløbet"). For a link sent to the wrong person. -->
                        <form class="js-confirm" action="<?= url('/invite') ?>" method="post"
                            data-confirm="Vil du annullere linket? Det kan ikke bruges bagefter.">
                            <?= \PostCard\Core\Csrf::field() ?>
                            <input type="hidden" name="invitation_id" value="7">
                            <input type="hidden" name="action" value="cancel">
                            <button class="button button--secondary" type="submit">Annuller link</button>
                        </form>
                    </li>
                    <li>
                        <p>Lavet <time datetime="2026-10-03">3. okt. 2026</time></p>
                        <p class="meta">Brugt <time datetime="2026-10-04">4. okt.</time></p>
                    </li>
                    <li>
                        <p>Lavet <time datetime="2026-09-25">25. sep. 2026</time></p>
                        <p class="meta">Udløbet <time datetime="2026-10-02">2. okt.</time> uden at blive brugt</p>
                    </li>
                </ul>
                <!-- /LOOP -->
            </section>
        </div>
    </div>
</main>