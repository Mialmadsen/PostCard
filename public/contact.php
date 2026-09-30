<?php
$pageTitle = 'Kontakt';
$isLoggedIn = false;
require __DIR__ . '/../views/partials/header.php';
?>

<main id="main" class="site-main">
    <div class="container container--text">
        <article class="card flow">
            <p class="eyebrow">PostCard</p>
            <h1>Kontakt</h1>

            <!-- CONTENT: contact information — managed by the operator in /admin/content (Epic 2) -->
            <p>Har du spørgsmål, eller vil du anmelde indhold, der bryder reglerne? Skriv til os, så svarer vi inden for to hverdage.</p>

            <address class="flow">
                <p><strong>E-mail:</strong> <a href="mailto:kontakt@postcard.example">kontakt@postcard.example</a></p>
                <p><strong>Adresse:</strong> <span class="brand">PostCard</span>, Eksempelvej 1, 6700 Esbjerg</p>
            </address>
        </article>
    </div>
</main>

<?php require __DIR__ . '/../views/partials/footer.php'; ?>
