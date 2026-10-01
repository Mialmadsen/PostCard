<?php
$pageTitle = 'Familien Nielsen';
$isLoggedIn = true;
$pageStyle = 'group';
require __DIR__ . '/../views/partials/header.php';
?>

<main id="main" class="site-main">
    <div class="container">
        <p class="eyebrow">Gruppe</p>
        <h1>Familien Nielsen</h1>
        <p>Rejser, ferier og weekendture med hele familien.</p>
        <p class="meta">13 medlemmer · Åben for opslag · Du er admin</p>

        <div class="sidebar-layout">

            <!-- Left column: about the group -->
            <div class="flow">
                <section class="card flow" aria-labelledby="share-heading">
                    <h2 id="share-heading">Del en oplevelse</h2>
                    <!-- IF posting_mode = 'open' -->
                    <p>Start en oplevelse for din rejse. Du kan dele op til 20 opslag med op til 5 billeder hver.</p>
                    <p><a class="button button--primary" href="create-experience.php?group=1">Start en ny oplevelse</a></p>
                    <!-- ELSE (posting_mode = 'permission'):
                    <p>I denne gruppe skal gruppens admin godkende din oplevelse, før du kan dele opslag.</p>
                    <p><a class="button button--primary" href="create-experience.php?group=1">Anmod om at dele en oplevelse</a></p>
                    -->
                </section>

                <section class="card flow" aria-labelledby="members-heading">
                    <h2 id="members-heading">Medlemmer</h2>
                    <!-- LOOP: first 5 members -->
                    <div class="avatar-stack" aria-hidden="true">
                        <span class="avatar">MN</span>
                        <span class="avatar">AN</span>
                        <span class="avatar">PN</span>
                        <span class="avatar">LN</span>
                        <span class="avatar">+9</span>
                    </div>
                    <!-- /LOOP -->
                    <p><a href="members.php?group=1">Se alle 13 medlemmer</a></p>
                </section>

                <!-- IF member_role = 'group_admin' -->
                <section class="card flow admin-box" aria-labelledby="admin-heading">
                    <h2 id="admin-heading">Gruppeadmin</h2>
                    <p class="meta">Kun du som admin kan se denne boks.</p>
                    <ul class="admin-links">
                        <li><a href="requests.php?group=1">Anmodninger (2 venter)</a></li>
                        <li><a href="invite.php?group=1">Inviter nye medlemmer</a></li>
                        <li><a href="group-settings.php?group=1">Gruppens indstillinger</a></li>
                    </ul>
                </section>
                <!-- /IF -->
            </div>

            <!-- Right column: the group's experiences -->
            <section aria-labelledby="experiences-heading">
                <h2 class="section-title" id="experiences-heading">Oplevelser</h2>

                <!-- LOOP: approved experiences in this group, newest first (experiences.created_at) -->
                <div class="experience-grid">
                    <article class="polaroid experience-card">
                        <img class="polaroid__photo" src="assets/images/sample/photo-4.jpg"
                            alt="" width="800" height="600" loading="lazy">
                        <div class="polaroid__body experience-card__body">
                            <p class="postmark postmark--status polaroid__postmark experience-card__postmark experience-card__postmark--active">I gang</p>
                            <h3 class="experience-card__title">
                                <a class="polaroid__link" href="experience.php?id=1">Sommer i Norge</a>
                            </h3>
                            <p class="meta">Mia Nielsen · <time datetime="2026-09-10">10. sep.</time> – <time datetime="2026-10-15">15. okt. 2026</time></p>
                            <p class="meta">8 opslag</p>
                        </div>
                    </article>

                    <article class="polaroid experience-card">
                        <img class="polaroid__photo" src="assets/images/sample/photo-5.jpg"
                            alt="" width="800" height="600" loading="lazy">
                        <div class="polaroid__body experience-card__body">
                            <p class="postmark postmark--status polaroid__postmark experience-card__postmark experience-card__postmark--ended">Afsluttet</p>
                            <h3 class="experience-card__title">
                                <a class="polaroid__link" href="experience.php?id=2">Roadtrip på Isle of Skye</a>
                            </h3>
                            <p class="meta">Anna Nielsen · <time datetime="2026-09-20">20.</time> – <time datetime="2026-09-30">30. sep. 2026</time></p>
                            <p class="meta">6 opslag</p>
                        </div>
                    </article>

                    <article class="polaroid experience-card">
                        <img class="polaroid__photo" src="assets/images/sample/photo-2.jpg"
                            alt="" width="800" height="600" loading="lazy">
                        <div class="polaroid__body experience-card__body">
                            <p class="postmark postmark--status polaroid__postmark experience-card__postmark experience-card__postmark--ended">Afsluttet</p>
                            <h3 class="experience-card__title">
                                <a class="polaroid__link" href="experience.php?id=3">Vinterferie i fjeldet</a>
                            </h3>
                            <p class="meta">Peter Nielsen · <time datetime="2026-02-14">14.</time> – <time datetime="2026-02-22">22. feb. 2026</time></p>
                            <p class="meta">15 opslag</p>
                        </div>
                    </article>

                    <article class="polaroid experience-card">
                        <img class="polaroid__photo" src="assets/images/sample/photo-1.jpg"
                            alt="" width="800" height="600" loading="lazy">
                        <div class="polaroid__body experience-card__body">
                            <p class="postmark postmark--status polaroid__postmark experience-card__postmark experience-card__postmark--ended">Afsluttet</p>
                            <h3 class="experience-card__title">
                                <a class="polaroid__link" href="experience.php?id=4">Nationalparker i USA</a>
                            </h3>
                            <p class="meta">Mia Nielsen · <time datetime="2025-07-01">1.</time> – <time datetime="2025-07-21">21. jul. 2025</time></p>
                            <p class="meta">20 opslag</p>
                        </div>
                    </article>
                </div>
                <!-- /LOOP -->
            </section>

        </div>
    </div>
</main>

<?php require __DIR__ . '/../views/partials/footer.php'; ?>
