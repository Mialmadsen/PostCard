<?php
$pageTitle = 'Velkommen';
$isLoggedIn = false;
$pageStyle = 'landing';
require __DIR__ . '/../views/partials/header.php';
?>

<main id="main" class="site-main">
    <div class="container">

        <!-- Hero -->
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

        <!-- CONTENT: site description — managed by the operator in /admin/content (Epic 2) -->
        <section class="landing-section card" aria-labelledby="why-heading">
            <h2 class="landing-section__title" id="why-heading">Hvorfor <span class="brand">PostCard</span>?</h2>

            <!-- LOOP: features -->
            <ul class="feature-list">
                <li class="feature flow">
                    <img class="feature__illustration" src="assets/images/illustrations/private.svg" alt="" width="145" height="156" loading="lazy">
                    <h3>Privat</h3>
                    <p>Kun de medlemmer, du inviterer, kan se det, du deler.</p>
                </li>
                <li class="feature flow">
                    <img class="feature__illustration" src="assets/images/illustrations/share.svg" alt="" width="223" height="144" loading="lazy">
                    <h3>Del med dem, du kender</h3>
                    <p>Familie, venner og klassekammerater – ikke fremmede.</p>
                </li>
                <li class="feature flow">
                    <img class="feature__illustration" src="assets/images/illustrations/focus.svg" alt="" width="162" height="161" loading="lazy">
                    <h3>Fokus på oplevelser</h3>
                    <p>Del et begrænset antal billeder fra hver rejse. Intet endeløst feed.</p>
                </li>
                <li class="feature flow">
                    <img class="feature__illustration" src="assets/images/illustrations/album.svg" alt="" width="174" height="165" loading="lazy">
                    <h3>Et digitalt album</h3>
                    <p>Jeres minder bliver gemt, så I kan se tilbage på dem senere.</p>
                </li>
            </ul>
            <!-- /LOOP -->
        </section>

        <section class="landing-section card" aria-labelledby="how-heading">
            <h2 class="landing-section__title" id="how-heading">Sådan virker det</h2>

            <ol class="step-list">
                <li class="step">
                    <span class="step__number postmark" aria-hidden="true">1</span>
                    <div class="flow">
                        <h3>Opret en gruppe</h3>
                        <p>Fx familien, holdet eller klassen. Du bliver gruppens admin.</p>
                    </div>
                </li>
                <li class="step">
                    <span class="step__number postmark" aria-hidden="true">2</span>
                    <div class="flow">
                        <h3>Inviter dem, du vil dele med</h3>
                        <p>Medlemmer kommer kun med via dit invitationslink.</p>
                    </div>
                </li>
                <li class="step">
                    <span class="step__number postmark" aria-hidden="true">3</span>
                    <div class="flow">
                        <h3>Del jeres oplevelser</h3>
                        <p>Saml billeder fra en rejse i en oplevelse, som hele gruppen kan følge.</p>
                    </div>
                </li>
            </ol>
        </section>

    </div>
</main>

<?php require __DIR__ . '/../views/partials/footer.php'; ?>
