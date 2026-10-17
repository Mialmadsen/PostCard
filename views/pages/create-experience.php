<?php
// Henter den første gruppe som standard, hvis brugeren har grupper
$selectedGroup = $groups[0] ?? null;
?>

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
                    <span id="breadcrumb-group">
                        <?= e($selectedGroup['name'] ?? 'Vælg gruppe') ?>
                    </span>
                </li>

                <li class="breadcrumb__item">
                    <span aria-current="page">Ny oplevelse</span>
                </li>
            </ol>
        </nav>

        <header class="page-header">
            <p class="group-label" id="header-group">
                <?= e($selectedGroup['name'] ?? 'Vælg gruppe') ?>
            </p>

            <h1>Ny oplevelse</h1>

            <p id="posting-description"></p>
        </header>

        <div class="sidebar-layout">

            <!-- Viser hvilken gruppe oplevelsen oprettes i -->
            <aside class="card flow" aria-labelledby="address-heading">
                <h2 id="address-heading">Til</h2>

                <dl class="address">
                    <div>
                        <dt class="group-label">Gruppe</dt>
                        <dd class="address__value" id="address-group">
                            <?= e($selectedGroup['name'] ?? 'Vælg gruppe') ?>
                        </dd>
                    </div>

                    <!-- Vises kun hvis gruppen kræver godkendelse -->
                    <div id="approval-info">
                        <dt class="group-label">Godkendes af</dt>
                        <dd class="address__value">Gruppeadministrator</dd>
                    </div>
                </dl>
            </aside>

            <section class="card" aria-labelledby="form-heading">
                <h2 class="section-title" id="form-heading">Din oplevelse</h2>

                <!-- Formularen sender senere data til vores store()-metode -->
                <form method="POST" action="<?= url('/create-experience') ?>">
                    <!-- Sikkerhedstoken, som kontrolleres, når formularen sendes -->
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e($_SESSION['csrf_token']) ?>"
                    >

                    <!-- Vælg mellem brugerens aktive grupper -->
                    <div class="field">
                        <label class="field__label" for="group">Gruppe</label>

                        <select class="field__input" id="group" name="group_id" required>
                            <option value="">Vælg en gruppe</option>

                            <?php foreach ($groups as $group): ?>
                                <option
                                    value="<?= (int) $group['group_id'] ?>"
                                    data-name="<?= e($group['name']) ?>"
                                    data-posting-mode="<?= e($group['posting_mode']) ?>"
                                    <?= $selectedGroup !== null && (int) $selectedGroup['group_id'] === (int) $group['group_id'] ? 'selected' : '' ?>
                                >
                                    <?= e($group['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="field">
                        <label class="field__label" for="name">
                            Navn på oplevelsen
                        </label>

                        <input
                            class="field__input"
                            id="name"
                            name="name"
                            type="text"
                            maxlength="100"
                            required
                            aria-describedby="name-hint"
                        >

                        <p class="meta" id="name-hint">
                            Fx Sommer i Norge eller Studietur til Berlin
                        </p>
                    </div>

                    <div class="field">
                        <label class="field__label" for="description">
                            Hvad handler den om?
                        </label>

                        <textarea
                            class="field__input"
                            id="description"
                            name="description"
                            rows="4"
                            maxlength="1000"
                            aria-describedby="description-hint"
                        ></textarea>

                        <p class="meta" id="description-hint">
                            Gruppen ser teksten øverst på oplevelsens side.
                            Du kan springe den over.
                        </p>
                    </div>

                    <!-- Serveren skal senere kontrollere datoerne -->
                    <fieldset class="fieldset" aria-describedby="period-hint">
                        <legend class="field__label fieldset__legend">
                            Periode
                        </legend>

                        <div class="field-row">
                            <div class="field">
                                <label for="start-date">Fra</label>

                                <input
                                    class="field__input"
                                    id="start-date"
                                    name="start_date"
                                    type="date"
                                    required
                                >
                            </div>

                            <div class="field">
                                <label for="end-date">Til</label>

                                <input
                                    class="field__input"
                                    id="end-date"
                                    name="end_date"
                                    type="date"
                                    required
                                >
                            </div>
                        </div>

                        <p class="meta" id="period-hint">
                            Du kan dele opslag fra den første til den sidste dag.
                        </p>
                    </fieldset>

                    <!-- Teksten ændres afhængigt af gruppens posting_mode -->
                    <p class="alert" id="experience-alert"></p>

                    <div class="button-row">
                        <button
                            class="button button--primary"
                            type="submit"
                            id="submit-button"
                            disabled
                        >
                            Send anmodning
                        </button>

                        <a class="button button--secondary" href="<?= url('/home') ?>">
                            Annuller
                        </a>
                    </div>

                </form>
            </section>

        </div>
    </div>
</main>

<script>
// Finder de HTML-elementer, vi skal opdatere
const groupSelect = document.getElementById('group');
const breadcrumbGroup = document.getElementById('breadcrumb-group');
const headerGroup = document.getElementById('header-group');
const addressGroup = document.getElementById('address-group');
const postingDescription = document.getElementById('posting-description');
const approvalInfo = document.getElementById('approval-info');
const experienceAlert = document.getElementById('experience-alert');
const submitButton = document.getElementById('submit-button');

// Opdaterer informationen, når brugeren vælger en gruppe
function updateGroupInformation() {

    // Finder den valgte gruppe i dropdown-menuen
    const selectedOption = groupSelect.selectedOptions[0];

    // Henter gruppens navn og posting_mode
    const groupName = selectedOption.dataset.name || 'Vælg gruppe';
    const postingMode = selectedOption.dataset.postingMode || '';

    // Opdaterer gruppenavnet forskellige steder på siden
    breadcrumbGroup.textContent = groupName;
    headerGroup.textContent = groupName;
    addressGroup.textContent = groupName;

    // Kontrollerer om gruppen kræver godkendelse
    if (postingMode === 'permission') {

        postingDescription.textContent =
            'I denne gruppe skal en gruppeadministrator godkende din oplevelse, før du kan dele opslag.';

        approvalInfo.hidden = false;

        experienceAlert.textContent =
            'Din anmodning bliver sendt til gruppens administratorer. Når oplevelsen er godkendt, kan du dele opslag i den valgte periode. Du kan følge status på din forside.';

        submitButton.textContent = 'Send anmodning';

    } else if (postingMode === 'open') {

        postingDescription.textContent =
            'Start en oplevelse, og del dine opslag med gruppen, mens den står på.';

        approvalInfo.hidden = true;

        experienceAlert.textContent =
            'Oplevelsen bliver oprettet med det samme i ' + groupName +
            '. Du kan dele opslag fra startdatoen.';

        submitButton.textContent = 'Opret oplevelse';

    } else {

        // Hvis der ikke er valgt en gruppe
        postingDescription.textContent =
            'Vælg en gruppe for at fortsætte.';

        approvalInfo.hidden = true;
        experienceAlert.textContent = '';
        submitButton.textContent = 'Opret oplevelse';
    }

        // Knappen aktiveres, når brugeren har valgt en gyldig gruppe
        submitButton.disabled = !groupSelect.value;
}

// Kører funktionen, når brugeren vælger en anden gruppe
groupSelect.addEventListener('change', updateGroupInformation);

// Opdaterer siden, når den indlæses
updateGroupInformation();
</script>
