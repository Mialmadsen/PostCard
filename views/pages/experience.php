
<!-- filem er HTML'en til den visuelle side, så den er adskilt fra SQL og controlleren. 
 Viewet bruger de variabler, som controlleren sender videre, til at vise oplevelsens titel, datoer, beskrivelse og status. -->

 <main id="main" class="site-main">
    <div class="container">

        <nav class="breadcrumb" aria-label="Du er her">
            <ol class="breadcrumb__list">

                <li class="breadcrumb__item">
                    <a class="breadcrumb__link" href="<?= url('/home') ?>">
                        Mine grupper
                    </a>
                </li>

                <li class="breadcrumb__item">
                    <a
                        class="breadcrumb__link"
                        href="<?= url('/group') ?>?id=<?= (int) $experience['group_id'] ?>">
                        <?php
                        // e() sikrer, at gruppens navn vises som tekst
                        // i stedet for at blive fortolket som HTML
                        ?>
                        <?= e($experience['group_name']) ?>
                    </a>
                </li>

                <li class="breadcrumb__item">
                    <a
                        class="breadcrumb__link"
                        href="<?= url('/experience') ?>?id=<?= (int) $experience['experience_id'] ?>"
                        aria-current="page">
                        <?= e($experience['name']) ?>
                    </a>
                </li>

            </ol>
        </nav>

        <header class="page-header experience-header">

            <p class="group-label">
                <?= e($experience['group_name']) ?>
            </p>

            <h1 class="experience-header__title">
                <?= e($experience['name']) ?>
            </h1>

            <p class="meta">

                <?php
                // Henter brugerens fornavn og efternavn fra arrayet
                // ?? '' betyder, at vi bruger en tom tekst, hvis værdien mangler
                // trim() fjerner unødvendige mellemrum
                ?>
                <?= e(
                    trim(($experience['first_name'] ?? '') . ' ' . ($experience['last_name'] ?? ''))
                ) ?>

                ·

                <time datetime="<?= e($experience['start_date']) ?>">
                    <?php // strtotime() og date() bruges til at formatere datoen ?>
                    <?= date('d. M Y', strtotime($experience['start_date'])) ?>
                </time>

                –

                <time datetime="<?= e($experience['end_date']) ?>">
                    <?= date('d. M Y', strtotime($experience['end_date'])) ?>
                </time>

                <?php // Status er allerede beregnet og oversat i controlleren ?>
                · <?= e($statusText) ?>

            </p>

            <p class="experience-header__text">
                <?= e($experience['description'] ?? '') ?>
            </p>

        </header>

        <section class="card experience-owner" aria-labelledby="owner-heading">

            <h2 id="owner-heading">Din oplevelse</h2>

            <?php
            // Kontrollerer om oplevelsen har en opslaggrænse
            // !== null betyder, at der findes en værdi
            // && betyder, at begge betingelser skal være opfyldt
            ?>
            <?php if ($postLimit !== null && $postLimit > 0): ?>

                <label class="meta" for="post-progress">

                    <?php // Viser antallet af opslag og hvor mange der er tilbage ?>
                    <?= $postsUsed ?> af <?= $postLimit ?> opslag brugt

                    · <?= $postsRemaining ?> tilbage

                    <?php
                    // Viser kun dage tilbage, hvis controlleren har beregnet dem
                    ?>
                    <?php if ($daysRemaining !== null): ?>
                        · slutter om <?= $daysRemaining ?> dage
                    <?php endif; ?>

                </label>

                <progress
                    class="experience-owner__progress"
                    id="post-progress"
                    value="<?= min($postsUsed, $postLimit) ?>"
                    max="<?= $postLimit ?>">
                    <?= $postsUsed ?> af <?= $postLimit ?>
                </progress>

            <?php else: ?>

                <p class="meta">
                    <?= $postsUsed ?> opslag

                    <?php if ($daysRemaining !== null): ?>
                        · slutter om <?= $daysRemaining ?> dage
                    <?php endif; ?>
                </p>

            <?php endif; ?>

            <?php
            // $isActive kommer fra controlleren og fortæller,
            // om oplevelsen er godkendt og inden for start- og slutdatoen
            ?>
            <?php if ($isActive): ?>

                <p>
                    <span class="button button--primary">
                        Nyt opslag
                    </span>
                </p>

            <?php else: ?>

                <p class="meta">
                    Det er ikke muligt at oprette opslag i øjeblikket.
                </p>

            <?php endif; ?>

        </section>

        <section class="experience-posts" aria-labelledby="posts-heading">

            <h2 class="section-title" id="posts-heading">
                <?php // count() tæller hvor mange opslag der er i arrayet ?>
                Opslag <span class="meta">(<?= count($posts) ?>)</span>
            </h2>

            <div class="post-grid">

                <?php
                // foreach gennemgår alle opslag fra databasen ét ad gangen
                // $post indeholder det aktuelle opslag i løkken
                ?>
                <?php foreach ($posts as $post): ?>

                    <?php
                    // Indlæser post-card.php for hvert opslag
                    // __DIR__ henviser til mappen, som denne fil ligger i
                    require __DIR__ . '/../partials/post-card.php';
                    ?>

                <?php endforeach; ?>

            </div>

            <?php
            // empty() kontrollerer, om arrayet med opslag er tomt
            ?>
            <?php if (empty($posts)): ?>

                <p class="meta">
                    Der er endnu ingen opslag i denne oplevelse.
                </p>

            <?php endif; ?>

        </section>

    </div>
</main>
