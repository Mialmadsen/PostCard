<?php
// Header og footer indlæses automatisk gennem View::render()
?>

<main id="main" class="site-main">
    <div class="container">
        <p class="eyebrow">Gruppe</p>
        <h1><?= e($group['name']) ?></h1>
        <p><?= e($group['description'] ?? '') ?></p>
        <p class="meta">
            <?= (int) $memberCount ?> medlemmer ·
            <?= $group['posting_mode'] === 'open' ? 'Åben for opslag' : 'Kræver godkendelse' ?>
            · <?= $memberRole === 'group_admin' ? 'Du er admin' : 'Du er medlem' ?>
        </p>

        <div class="sidebar-layout">

            <!-- Left column: about the group -->
            <div class="flow">
            
                <section class="card flow" aria-labelledby="share-heading">
                    <h2 id="share-heading">Del en oplevelse</h2>
                    <?php if ($group['posting_mode'] === 'open' || $memberRole === 'group_admin'): ?>
                        <?php if ($group['posting_mode'] === 'open'): ?>
                            <p>Start en oplevelse for din rejse. Du kan dele opslag uden en fast opslagsgrænse.</p>
                        <?php else: ?>
                            <p>Som gruppeadministrator kan du oprette oplevelser uden at anmode om godkendelse.</p>
                        <?php endif; ?>
                        <p><a class="button button--primary" href="<?= url('/create-experience?group_id=' . (int) $group['group_id']) ?>">Start en ny oplevelse</a></p>
                    <?php else: ?>
                        <p>I denne gruppe skal gruppens admin godkende din oplevelse, før du kan dele opslag.</p>
                        <p><a class="button button--primary" href="<?= url('/create-experience?group_id=' . (int) $group['group_id']) ?>">Anmod om at dele en oplevelse</a></p>
                    <?php endif; ?>
                </section>


                <section class="card flow" aria-labelledby="members-heading">
                    <h2 id="members-heading">Medlemmer</h2>
                    <!-- Viser de første fire aktive medlemmer fra databasen -->
                    <div class="avatar-stack" aria-hidden="true">
                        <?php foreach ($members as $member): ?>
                            <span class="avatar"><?= e(initials($member['first_name'], $member['last_name'])) ?></span>
                        <?php endforeach; ?>

                        <!-- Viser antallet af resterende medlemmer -->
                        <?php if ($memberCount > count($members)): ?>
                            <span class="avatar">+<?= $memberCount - count($members) ?></span>
                        <?php endif; ?>
                    </div>

                    <p class="meta"><?= (int) $memberCount ?> medlemmer i gruppen</p>
                    <!-- /LOOP -->
                    <?php
                    // TODO: Tilføj link til medlemsoversigten, når /members-routen er lavet.
                    // Linket skal bruge gruppens dynamiske ID og medlemstal.
                    // tilføj også invitationslink til at invitere nye medlemmer, når /invite-routen er lavet.
                    ?>
                    <p><a href="<?= url('/members?group_id=' . (int) $group['group_id']) ?>">Se alle <?= (int) $memberCount ?> medlemmer</a></p>

                    <!-- Sets group_members.member_status = 'left'. The user id comes from the session, not the form.
                         IF the user is the only admin: no button, instead
                         <p class="meta">Du er den eneste admin. Gør et andet medlem til admin, før du forlader gruppen.</p> -->
                         <?php
                        // TODO: Den sidste gruppeadministrator må ikke kunne forlade gruppen,
                        // før et andet medlem er gjort til administrator.
                        ?>
                    <form class="js-confirm" action="<?= url('/leave-group') ?>" method="post"
                        data-confirm="Vil du forlade <?= e($group['name']) ?>? Det, du har delt, bliver i gruppens album. Du skal have en ny invitation for at komme tilbage.">
                        <?= \PostCard\Core\Csrf::field() ?>
                        <input type="hidden" name="group_id" value="<?= (int) $group['group_id'] ?>">
                        <button class="button button--secondary" type="submit">Forlad gruppen</button>
                    </form>
                </section>

                <?php if ($memberRole === 'group_admin'): ?>
                    <!-- Vises kun for gruppens administratorer -->
                    <section class="card flow admin-box" aria-labelledby="admin-heading">
                        <h2 id="admin-heading">Gruppeadmin</h2>
                        <p class="meta">Kun du som admin kan se denne boks.</p>
                        <ul class="admin-links">
                            <li>
                                <a href="<?= url('/requests?group_id=' . (int) $group['group_id']) ?>">
                                    Anmodninger
                                </a>
                            </li>
                            <li>
                                <a href="<?= url('/invite?group_id=' . (int) $group['group_id']) ?>">
                                    Inviter nye medlemmer
                                </a>
                            </li>
                            <li>
                                <a href="<?= url('/group-settings?group_id=' . (int) $group['group_id']) ?>">
                                    Gruppens indstillinger
                                </a>
                            </li>
                        </ul>
                    </section>
                <?php endif; ?>
            </div>

            
            <!-- Højre kolonne: Gruppens oplevelser -->
            <section aria-labelledby="experiences-heading">
                <h2 class="section-title" id="experiences-heading">Oplevelser</h2>

                <div class="experience-grid">

                    <?php if (empty($experiences)): ?>
                        <!-- Vises, hvis gruppen endnu ikke har nogen godkendte oplevelser -->
                        <p class="meta">Der er endnu ingen oplevelser i denne gruppe.</p>

                    <?php else: ?>

                        <!-- Gennemgår alle gruppens oplevelser fra databasen -->
                        <?php foreach ($experiences as $experience): ?>

                            <?php
                            // Finder oplevelsens status ud fra datoerne og dens status i databasen
                            $today = date('Y-m-d');

                            if (
                                $experience['experience_status'] === 'closed' ||
                                $experience['end_date'] < $today
                            ) {
                                $statusText = 'Afsluttet';
                                $statusClass = 'experience-card__postmark--ended';
                            } elseif ($experience['start_date'] > $today) {
                                $statusText = 'Kommende';
                                $statusClass = 'experience-card__postmark--upcoming';
                            } else {
                                $statusText = 'I gang';
                                $statusClass = 'experience-card__postmark--active';
                            }

                            // Opretter linket til den enkelte oplevelse
                            $experienceUrl = url('/experience?id=' . (int)$experience['experience_id']);

                            // Formaterer datoerne til dansk visning med den fælles helper
                            $startFormatted = danishDate($experience['start_date'], 'dd.MM.y');
                            $endFormatted = danishDate($experience['end_date'], 'dd.MM.y');

                            ?>
                            <article class="polaroid experience-card">

                                <!-- Midlertidigt eksempelbillede, indtil oplevelser får rigtige billeder -->
                                <a href="<?= e($experienceUrl) ?>" tabindex="-1" aria-hidden="true">
                                    <img
                                        class="polaroid__photo"
                                        src="<?= e(asset('assets/images/sample/photo-4.jpg')) ?>"
                                        alt=""
                                        width="800"
                                        height="600"
                                        loading="lazy">
                                </a>

                                <div class="polaroid__body experience-card__body">

                                    <!-- Oplevelsens status -->
                                    <p class="postmark postmark--status polaroid__postmark experience-card__postmark <?= e($statusClass) ?>">
                                        <?= e($statusText) ?>
                                    </p>

                                    <!-- Oplevelsens navn og link -->
                                    <h3 class="polaroid__title">
                                        <a class="polaroid__link stretched-link" href="<?= e($experienceUrl) ?>">
                                            <?= e($experience['name']) ?>
                                        </a>
                                    </h3>

                                    <!-- Opretterens navn og oplevelsens periode -->
                                    <p class="meta">
                                        <?= e($experience['first_name'] . ' ' . $experience['last_name']) ?>
                                        ·
                                        <time datetime="<?= e($experience['start_date']) ?>">
                                            <?= e($startFormatted) ?>
                                        </time>
                                        –
                                        <time datetime="<?= e($experience['end_date']) ?>">
                                            <?= e($endFormatted) ?>
                                        </time>
                                    </p>

                                    <!-- Antal opslag i oplevelsen -->
                                    <p class="meta">
                                        <?= (int)$experience['post_count'] ?> opslag
                                    </p>

                                </div>
                            </article>

                        <?php endforeach; ?>

                    <?php endif; ?>

                </div>
            </section>


        </div>
    </div>
</main>

