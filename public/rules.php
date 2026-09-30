<?php
$pageTitle = 'Regler';
$isLoggedIn = false;
require __DIR__ . '/../views/partials/header.php';
?>

<main id="main" class="site-main">
    <div class="container container--text">
        <article class="card flow">
            <p class="eyebrow">PostCard</p>
            <h1>Regler og retningslinjer</h1>

            <!-- CONTENT: rules — managed by the operator in /admin/content (Epic 2) -->
            <p><span class="brand">PostCard</span> er et privat sted for grupper, der kender hinanden. For at det skal føles trygt for alle, gælder disse regler:</p>

            <ol class="flow">
                <li>Del kun billeder, du selv har taget eller har lov til at dele.</li>
                <li>Spørg, før du deler billeder af andre – især af børn.</li>
                <li>Det, der deles i en gruppe, bliver i gruppen. Del det ikke videre uden for <span class="brand">PostCard</span>.</li>
                <li>Intet krænkende, voldeligt eller ulovligt indhold.</li>
                <li>Skriv venligt og respektfuldt i kommentarer.</li>
                <li>Gruppens admin kan fjerne opslag og medlemmer, der bryder reglerne. <span class="brand">PostCard</span> kan blokere brugere.</li>
            </ol>

            <h2>Dine data</h2>
            <p>Du kan altid slette din bruger. Så bliver dine oplysninger og dine opslag slettet.</p>
        </article>
    </div>
</main>

<?php require __DIR__ . '/../views/partials/footer.php'; ?>
