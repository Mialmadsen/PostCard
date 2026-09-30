<?php
$pageTitle = 'Velkommen';
$isLoggedIn = false;
require __DIR__ . '/../views/partials/header.php';
?>

<main id="main" class="site-main">
    <div class="container">
        <div class="envelope">
            <div class="envelope__back" aria-hidden="true"></div>
            <div class="envelope__letter flow">
                <p class="eyebrow">Hilsen fra PostCard</p>
                <h1>Del din oplevelse – ikke hele dit liv</h1>
                <p>Et privat sted at dele rejseminder med de mennesker, der betyder noget.</p>
                <p><a class="button button--primary" href="register.php">Opret bruger</a></p>
            </div>
            <div class="envelope__front" aria-hidden="true"></div>
            <div class="envelope__lid" aria-hidden="true"></div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../views/partials/footer.php'; ?>
