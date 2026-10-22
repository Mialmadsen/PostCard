<?php
// Sidens indhold bliver vist gennem vores fælles View::render()
// Header og footer bliver derfor indlæst automatisk
?>

<main id="main" class="site-main">
    <div class="container container--text">
        <nav class="breadcrumb" aria-label="Du er her">
            <ol class="breadcrumb__list">
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="home.php">Mine grupper</a></li>

                <!-- Viser gruppens navn uden link, indtil gruppesiden er lavet -->
                <li class="breadcrumb__item">
                    <span><?= e($groupName) ?></span>
                </li>

                <li class="breadcrumb__item">
                    <a class="breadcrumb__link"
                    href="<?= url('/requests?group_id=' . (int) $groupId) ?>"
                    aria-current="page">
                        Anmodninger
                    </a>
                </li>
            </ol>
        </nav>

        <header class="page-header">
            <p class="group-label"><?= e($groupName) ?> · kun for admins</p>
            <h1>Anmodninger</h1>
            <p>Medlemmer, der vil dele en oplevelse. Du kan godkende den, ændre perioden eller antallet af opslag først, eller afvise den.</p>
        </header>

        <!-- Viser en bekræftelse efter godkendelse eller afvisning -->
        <!-- Beskeden kommer fra controlleren via Session::takeFlash() -->
        <?php if ($success !== null): ?>

            <p class="alert" role="status">
                <?= e($success) ?>
            </p>

        <?php endif; ?>


        <!-- Dropdown til at vælge hvilken gruppes anmodninger der skal vises -->
        <form class="form" action="<?= url('/requests') ?>" method="get">
            <div class="field">
                <label class="field__label" for="group_id">Vælg gruppe</label>

                <select
                    class="field__input"
                    id="group_id"
                    name="group_id"
                    onchange="this.form.submit()"
                >
                    <?php foreach ($groups as $group): ?>
                        <option
                            value="<?= e($group['group_id']) ?>"
                            <?= (int) $group['group_id'] === (int) $groupId ? 'selected' : '' ?>
                        >
                            <?= e($group['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button class="button button--secondary" type="submit">
                Vis anmodninger
            </button>
        </form>


        <?php if (empty($requests)): ?>

            <!-- Vises hvis gruppen ikke har nogen oplevelser der afventer godkendelse -->
            <p class="alert">Der er ingen anmodninger lige nu.</p>

        <?php else: ?>

            <!-- Viser de oplevelser der afventer godkendelse -->
            <div class="card-stack">

                <?php foreach ($requests as $request): ?>

                    <?php
                    // Henter oplysningerne om den enkelte anmodning
                    $experienceId = (int) $request['experience_id'];
                    $authorName = trim($request['first_name'] . ' ' . $request['last_name']);
                    ?>

                    <article class="card flow" aria-labelledby="request-<?= $experienceId ?>-heading">

                        <!-- Viser hvem der har oprettet oplevelsen og hvornår -->
                        <p class="group-label">
                            Fra <?= e($authorName) ?> · sendt
                            <time datetime="<?= e($request['created_at']) ?>">
                                <?= e(date('d.m.Y', strtotime($request['created_at']))) ?>
                            </time>
                        </p>

                        <h2 id="request-<?= $experienceId ?>-heading">
                            <?= e($request['name']) ?>
                        </h2>

                        <p><?= e($request['description']) ?></p>

                        <!-- Formular til at godkende oplevelsen og eventuelt ændre datoer og post limit -->
                        <form
                            class="form"
                            id="approve-<?= $experienceId ?>"
                            action="<?= url('/requests') ?>"
                            method="post"
                        >
                            <?= \PostCard\Core\Csrf::field() ?>

                            <input type="hidden" name="experience_id" value="<?= $experienceId ?>">
                            <input type="hidden" name="decision" value="approve">

                            <fieldset class="fieldset">
                                <legend class="field__label fieldset__legend">Periode</legend>

                                <div class="field-row">
                                    <div class="field">
                                        <label for="start-date-<?= $experienceId ?>">Fra</label>
                                        <input
                                            class="field__input"
                                            id="start-date-<?= $experienceId ?>"
                                            name="start_date"
                                            type="date"
                                            value="<?= e($request['start_date']) ?>"
                                            required
                                        >
                                    </div>

                                    <div class="field">
                                        <label for="end-date-<?= $experienceId ?>">Til</label>
                                        <input
                                            class="field__input"
                                            id="end-date-<?= $experienceId ?>"
                                            name="end_date"
                                            type="date"
                                            value="<?= e($request['end_date']) ?>"
                                            required
                                        >
                                    </div>
                                </div>
                            </fieldset>

                            <div class="field">
                                <label class="field__label" for="post-limit-<?= $experienceId ?>">
                                    Antal opslag
                                </label>

                                <input
                                    class="field__input field__input--short"
                                    id="post-limit-<?= $experienceId ?>"
                                    name="post_limit"
                                    type="number"
                                    min="1"
                                    max="50"
                                    value="<?= e($request['post_limit']) ?>"
                                    required
                                >

                                <p class="meta">
                                    Hvert opslag kan have op til 5 billeder.
                                </p>
                            </div>
                        </form>

                        <div class="button-row">

                            <!-- Godkend-knappen sender formularen ovenfor -->
                            <button
                                class="button button--primary"
                                type="submit"
                                form="approve-<?= $experienceId ?>"
                            >
                                Godkend
                            </button>

                            <!-- Separat formular til at afvise oplevelsen -->
                            <form
                                class="js-confirm"
                                action="<?= url('/requests') ?>"
                                method="post"
                                data-confirm="Vil du afvise <?= e($request['name']) ?>?"
                            >
                                <?= \PostCard\Core\Csrf::field() ?>

                                <input type="hidden" name="experience_id" value="<?= $experienceId ?>">
                                <input type="hidden" name="decision" value="reject">

                                <button class="button button--secondary" type="submit">
                                    Afvis
                                </button>
                            </form>
                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>
</main>
