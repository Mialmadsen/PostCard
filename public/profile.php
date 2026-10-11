<?php
$pageTitle = 'Min profil';
$isLoggedIn = true;
$pageStyle = 'profile';
require __DIR__ . '/../views/partials/header.php';
?>

<main id="main" class="site-main">
    <div class="container">
        <p class="eyebrow">Min profil</p>
        <h1>Mia Nielsen</h1>
        <p>Dit eget album i tal. Tallene viser kun dine egne oplevelser – vi sammenligner dig aldrig med andre.</p>

        <div class="sidebar-layout">

            <!-- Left column: who I am (users table) -->
            <section class="card flow profile-card" aria-labelledby="about-heading">
                <h2 class="visually-hidden" id="about-heading">Om mig</h2>
                <!-- IF users.profile_image: <img class="avatar avatar--large" src="…" alt=""> ELSE initials -->
                <span class="avatar avatar--large" aria-hidden="true">MN</span>
                <p class="meta">@mianielsen</p>
                <p><a class="button button--secondary" href="settings.php">Rediger profil</a></p>
            </section>

            <!-- Right column: the infographic -->
            <div class="flow">

                <section class="card" aria-labelledby="numbers-heading">
                    <h2 class="section-title" id="numbers-heading">Mit album i tal</h2>

                    <!-- Counts for the logged-in user only -->
                    <dl class="stat-list">
                        <div class="stat-list__item">
                            <dt>Grupper</dt>
                            <dd class="stat-list__number">3</dd>
                        </div>
                        <div class="stat-list__item">
                            <dt>Oplevelser</dt>
                            <dd class="stat-list__number">6</dd>
                        </div>
                        <div class="stat-list__item">
                            <dt>Opslag</dt>
                            <dd class="stat-list__number">38</dd>
                        </div>
                        <div class="stat-list__item">
                            <dt>Billeder</dt>
                            <dd class="stat-list__number">124</dd>
                        </div>
                    </dl>

                    <h3 class="profile-subheading">Modtagne reaktioner</h3>
                    <!-- Likes and comments other members gave on my posts -->
                    <dl class="stat-list">
                        <div class="stat-list__item">
                            <dt>Synes godt om</dt>
                            <dd class="stat-list__number">186</dd>
                        </div>
                        <div class="stat-list__item">
                            <dt>Kommentarer</dt>
                            <dd class="stat-list__number">54</dd>
                        </div>
                    </dl>
                </section>

                <section class="card" aria-labelledby="timeline-heading">
                    <h2 class="section-title" id="timeline-heading">Opslag pr. måned</h2>
                    <p class="meta">De seneste 12 måneder.</p>

                    <!-- LOOP: the last 12 months, oldest first.
                         --posts = posts that month, --max (on the list) = the highest month,
                         so the tallest bar always fills the chart. -->
                    <ol class="month-chart" style="--max: 5">
                        <li class="month-chart__month" style="--posts: 0">
                            <span class="month-chart__count" aria-hidden="true">0</span>
                            <span class="month-chart__bar" aria-hidden="true"></span>
                            <span class="month-chart__label" aria-hidden="true">nov</span>
                            <span class="visually-hidden">November 2025: 0 opslag</span>
                        </li>
                        <li class="month-chart__month" style="--posts: 5">
                            <span class="month-chart__count" aria-hidden="true">5</span>
                            <span class="month-chart__bar" aria-hidden="true"></span>
                            <span class="month-chart__label" aria-hidden="true">dec</span>
                            <span class="visually-hidden">December 2025: 5 opslag</span>
                        </li>
                        <li class="month-chart__month" style="--posts: 0">
                            <span class="month-chart__count" aria-hidden="true">0</span>
                            <span class="month-chart__bar" aria-hidden="true"></span>
                            <span class="month-chart__label" aria-hidden="true">jan</span>
                            <span class="visually-hidden">Januar 2026: 0 opslag</span>
                        </li>
                        <li class="month-chart__month" style="--posts: 0">
                            <span class="month-chart__count" aria-hidden="true">0</span>
                            <span class="month-chart__bar" aria-hidden="true"></span>
                            <span class="month-chart__label" aria-hidden="true">feb</span>
                            <span class="visually-hidden">Februar 2026: 0 opslag</span>
                        </li>
                        <li class="month-chart__month" style="--posts: 4">
                            <span class="month-chart__count" aria-hidden="true">4</span>
                            <span class="month-chart__bar" aria-hidden="true"></span>
                            <span class="month-chart__label" aria-hidden="true">mar</span>
                            <span class="visually-hidden">Marts 2026: 4 opslag</span>
                        </li>
                        <li class="month-chart__month" style="--posts: 0">
                            <span class="month-chart__count" aria-hidden="true">0</span>
                            <span class="month-chart__bar" aria-hidden="true"></span>
                            <span class="month-chart__label" aria-hidden="true">apr</span>
                            <span class="visually-hidden">April 2026: 0 opslag</span>
                        </li>
                        <li class="month-chart__month" style="--posts: 3">
                            <span class="month-chart__count" aria-hidden="true">3</span>
                            <span class="month-chart__bar" aria-hidden="true"></span>
                            <span class="month-chart__label" aria-hidden="true">maj</span>
                            <span class="visually-hidden">Maj 2026: 3 opslag</span>
                        </li>
                        <li class="month-chart__month" style="--posts: 0">
                            <span class="month-chart__count" aria-hidden="true">0</span>
                            <span class="month-chart__bar" aria-hidden="true"></span>
                            <span class="month-chart__label" aria-hidden="true">jun</span>
                            <span class="visually-hidden">Juni 2026: 0 opslag</span>
                        </li>
                        <li class="month-chart__month" style="--posts: 0">
                            <span class="month-chart__count" aria-hidden="true">0</span>
                            <span class="month-chart__bar" aria-hidden="true"></span>
                            <span class="month-chart__label" aria-hidden="true">jul</span>
                            <span class="visually-hidden">Juli 2026: 0 opslag</span>
                        </li>
                        <li class="month-chart__month" style="--posts: 0">
                            <span class="month-chart__count" aria-hidden="true">0</span>
                            <span class="month-chart__bar" aria-hidden="true"></span>
                            <span class="month-chart__label" aria-hidden="true">aug</span>
                            <span class="visually-hidden">August 2026: 0 opslag</span>
                        </li>
                        <li class="month-chart__month" style="--posts: 4">
                            <span class="month-chart__count" aria-hidden="true">4</span>
                            <span class="month-chart__bar" aria-hidden="true"></span>
                            <span class="month-chart__label" aria-hidden="true">sep</span>
                            <span class="visually-hidden">September 2026: 4 opslag</span>
                        </li>
                        <li class="month-chart__month" style="--posts: 2">
                            <span class="month-chart__count" aria-hidden="true">2</span>
                            <span class="month-chart__bar" aria-hidden="true"></span>
                            <span class="month-chart__label" aria-hidden="true">okt</span>
                            <span class="visually-hidden">Oktober 2026: 2 opslag</span>
                        </li>
                    </ol>
                    <!-- /LOOP -->
                </section>

                <section class="card" aria-labelledby="my-groups-heading">
                    <h2 class="section-title" id="my-groups-heading">Mine grupper og oplevelser</h2>

                    <!-- LOOP: my groups (group_members), each with my experiences in it (experiences.user_id = me) -->
                    <ul class="divided-list">
                        <li class="flow">
                            <h3><a href="group.php?id=1">Familien Nielsen</a></h3>
                            <p class="meta">Admin · 3 oplevelser · 31 opslag</p>
                            <ul class="profile-experiences">
                                <li><a href="experience.php?id=1">Sommer i Norge</a> <span class="meta">· I gang · 6 opslag</span></li>
                                <li><a href="experience.php?id=5">Jul hos mormor</a> <span class="meta">· Afsluttet · 5 opslag</span></li>
                                <li><a href="experience.php?id=4">Nationalparker i USA</a> <span class="meta">· Afsluttet · 20 opslag</span></li>
                            </ul>
                        </li>
                        <li class="flow">
                            <h3><a href="group.php?id=2">Klasse 3.B</a></h3>
                            <p class="meta">Medlem · 2 oplevelser · 4 opslag</p>
                            <ul class="profile-experiences">
                                <li>Studietur til Berlin <span class="meta">· Venter på godkendelse</span></li>
                                <li><a href="experience.php?id=6">Lejrskole på Bornholm</a> <span class="meta">· Afsluttet · 4 opslag</span></li>
                            </ul>
                        </li>
                        <li class="flow">
                            <h3><a href="group.php?id=3">Løbeklubben</a></h3>
                            <p class="meta">Medlem · 1 oplevelse · 3 opslag</p>
                            <ul class="profile-experiences">
                                <li><a href="experience.php?id=7">Halvmaraton i Aarhus</a> <span class="meta">· Afsluttet · 3 opslag</span></li>
                            </ul>
                        </li>
                    </ul>
                    <!-- /LOOP -->
                </section>

            </div>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../views/partials/footer.php'; ?>
