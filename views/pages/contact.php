<main id="main" class="site-main">
    <div class="container container--text">
        <article class="card flow">
            <img class="card__illustration" src="<?= asset('assets/images/illustrations/travel.svg') ?>" alt="" width="191" height="166">

            <p class="eyebrow">PostCard</p>

            <h1><?= e($siteContent['contact']['title'] ?? 'Kontakt') ?></h1>

            <?php /* CONTENT: contact information — managed by the operator in /admin/content (Epic 2) */ ?>

            <!-- Henter kontaktoplysningerne fra databasen -->
            <address class="flow">
                <?= nl2br(e($siteContent['contact']['body'] ?? 'Kontaktoplysninger er undervejs.')) ?>
            </address>
        </article>
    </div>
</main>
