<?php
// abort() shows this page with:
// View::render('error', ['pageTitle' => 'Fejl 404', 'isLoggedIn' => …, 'pageStyle' => 'error', 'code' => 404, 'message' => 'Opslaget blev ikke fundet.']);

/** @var int $code        the HTTP status code, e.g. 404 */
/** @var string $message  what went wrong, written where abort() was called */
/** @var bool $isLoggedIn */

// One postal text per error code. Any other code counts as a failed delivery.
$texts = [
    400 => [
        'stamp' => 'Ulæselig adresse',
        'title' => 'Ups! Adressen er ulæselig',
        'text' => 'Postkortet kom frem med en adresse, vi ikke kunne tyde. Tjek linket, eller gå tilbage og prøv igen.',
    ],
    403 => [
        'stamp' => 'Afvist ved skranken',
        'title' => 'Ups! Postkortet blev stoppet',
        'text' => 'Postbuddet afleverer kun, når alt stemmer – og her stemte noget ikke. Læs beskeden fra postkontoret nedenfor.',
    ],
    404 => [
        'stamp' => 'Ukendt modtager',
        'title' => 'Ups! Vi kan ikke finde modtageren',
        'text' => 'Postbuddet har ledt i hele postsækken, men siden findes ikke. Måske er den flyttet uden at efterlade en ny adresse.',
    ],
];
$failedDelivery = [
    'stamp' => 'Levering mislykkedes',
    'title' => 'Ups! Postkortet blev væk undervejs',
    'text' => 'Noget gik galt hos os. Prøv igen om lidt – postbuddet er allerede ude at lede.',
];
$text = $texts[$code] ?? $failedDelivery;
?>

<main id="main" class="site-main">
    <div class="container">
        <div class="error-page">

            <?php /* Decorative: the heading and the text say what happened */ ?>
            <img class="error-page__illustration" src="<?= asset('assets/images/illustrations/error-postman.svg') ?>" alt=""
                width="1080" height="1332">

            <section class="card flow error-page__card" aria-labelledby="error-heading">
                <p class="eyebrow">Fejl <?= (int) $code ?></p>

                <?php /* The stamp repeats the heading in postal words, so screen readers skip it */ ?>
                <p class="error-page__stamp" aria-hidden="true"><?= e($text['stamp']) ?></p>

                <h1 id="error-heading"><?= e($text['title']) ?></h1>
                <p><?= e($text['text']) ?></p>
                <p class="meta">Besked fra postkontoret: <?= e($message) ?></p>

                <p class="button-row">
                    <?php if ($isLoggedIn): ?>
                        <a class="button button--primary" href="<?= url('/home') ?>">Til mine grupper</a>
                    <?php else: ?>
                        <a class="button button--primary" href="<?= url('/') ?>">Til forsiden</a>
                    <?php endif; ?>
                </p>
            </section>

        </div>
    </div>
</main>
