<?php
// Henter reglerne fra databasen og deler dem op i linjer.
$rulesText = $siteContent['rules']['body'] ?? '';

// Hver linje bliver til et punkt på listen.
$rules = preg_split('/\R/u', $rulesText) ?: [];

// Fjerner tomme linjer.
$rules = array_values(array_filter(
    array_map('trim', $rules),
    static fn(string $rule): bool => $rule !== ''
));
?>

<main id="main" class="site-main">
    <div class="container container--text">
        <article class="card flow">
            <p class="eyebrow">PostCard</p>

            <h1>
                <?= e($siteContent['rules']['title'] ?? 'Regler og retningslinjer') ?>
            </h1>

            <?php /* CONTENT: rules — managed by the operator in /admin/content (Epic 2) */ ?>

            <p>
                <span class="brand">PostCard</span>
                er et privat sted for grupper, der kender hinanden.
                For at det skal føles trygt for alle, gælder disse regler:
            </p>

            <ol class="flow">
                <?php foreach ($rules as $rule): ?>
                    <li><?= e($rule) ?></li>
                <?php endforeach; ?>
            </ol>

            <h2>
                <?= e($siteContent['privacy']['title'] ?? 'Dine data') ?>
            </h2>

            <p>
                <?= nl2br(e($siteContent['privacy']['body'] ?? '')) ?>
            </p>

        </article>
    </div>
</main>
