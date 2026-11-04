<?php

/** @var array $group  the group as it is saved: breadcrumbs, header, hidden id, cancel link */
/** @var array $old    what the form fields show: the saved group, or what the admin typed */
/** @var array $errors one message per field that failed the check */
/** @var string|null $settingsMessage */

?>

<main id="main" class="site-main">
    <div class="container container--text">
        <?php
        $breadcrumbs = [
            ['label' => 'Mine grupper', 'path' => '/home'],
            ['label' => $group['name'], 'path' => '/group?id=' . $group['group_id']],
            ['label' => 'Indstillinger', 'path' => '/group-settings?group_id=' . $group['group_id']],
        ];
        require __DIR__ . '/../partials/breadcrumbs.php';
        ?>

        <header class="page-header">
            <p class="group-label"><?= e($group['name']) ?> · kun for admins</p>
            <h1>Gruppens indstillinger</h1>
        </header>
        <?php if ($settingsMessage !== null): ?>
            <p class="alert" role="status"><?= e($settingsMessage) ?></p>
        <?php endif; ?>

        <div class="card-stack">
            <section class="card" aria-labelledby="form-heading">
                <h2 class="section-title" id="form-heading">Om gruppen</h2>

                <?php if ($errors !== []): ?>
                    <p class="alert alert--error" role="alert">Noget mangler eller er forkert. Se de markerede felter
                        nedenfor.</p>
                <?php endif; ?>

                <form class="form" action="<?= url('/group-settings') ?>" method="post">
                    <?= \PostCard\Core\Csrf::field() ?>
                    <input type="hidden" name="group_id" value="<?= (int) $group['group_id'] ?>">

                    <div class="field">
                        <label class="field__label" for="name">Gruppens navn</label>
                        <input class="field__input" id="name" name="name" type="text" maxlength="100" required
                            value="<?= e($old['name'] ?? '') ?>"
                            <?php if (isset($errors['name'])): ?>aria-invalid="true" aria-describedby="name-error"
                            <?php endif; ?>>
                        <?php if (isset($errors['name'])): ?>
                            <p class="field__error" id="name-error"><?= e($errors['name']) ?></p>
                        <?php endif; ?>
                    </div>

                    <div class="field">
                        <label class="field__label" for="description">Beskrivelse</label>
                        <textarea class="field__input" id="description" name="description" rows="3" maxlength="1000"
                            <?php if (isset($errors['description'])): ?>aria-invalid="true"
                            aria-describedby="description-hint description-error"
                            <?php else: ?>aria-describedby="description-hint"
                            <?php endif; ?>><?= e($old['description'] ?? '') ?></textarea>
                        <p class="meta" id="description-hint">Medlemmerne ser den øverst på gruppens side.</p>
                        <?php if (isset($errors['description'])): ?>
                            <p class="field__error" id="description-error"><?= e($errors['description']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- user_groups.posting_mode -->
                    <?php $postingMode = $old['posting_mode'] ?? 'open'; ?>
                    <fieldset class="fieldset">
                        <legend class="field__label fieldset__legend">Hvem må dele oplevelser?</legend>
                        <div class="field field--checkbox">
                            <input class="field__checkbox" id="mode-open" name="posting_mode" type="radio" value="open"
                                <?php if ($postingMode === 'open'): ?>checked<?php endif; ?>
                                <?php if (isset($errors['posting_mode'])): ?>aria-invalid="true"
                                aria-describedby="mode-open-hint posting-mode-error"
                                <?php else: ?>aria-describedby="mode-open-hint" <?php endif; ?>>
                            <div>
                                <label for="mode-open">Alle medlemmer</label>
                                <p class="meta" id="mode-open-hint">Medlemmerne kan starte en oplevelse med det samme.
                                    Anmodninger, der allerede venter, skal stadig godkendes under Anmodninger.</p>
                            </div>
                        </div>
                        <div class="field field--checkbox">
                            <input class="field__checkbox" id="mode-permission" name="posting_mode" type="radio"
                                value="permission" <?php if ($postingMode === 'permission'): ?>checked<?php endif; ?>
                                <?php if (isset($errors['posting_mode'])): ?>aria-invalid="true"
                                aria-describedby="mode-permission-hint posting-mode-error"
                                <?php else: ?>aria-describedby="mode-permission-hint" <?php endif; ?>>
                            <div>
                                <label for="mode-permission">Kun med tilladelse</label>
                                <p class="meta" id="mode-permission-hint">En admin skal godkende hver oplevelse, før
                                    medlemmet kan dele opslag. Oplevelser, der allerede er i gang, fortsætter.</p>
                            </div>
                        </div>
                        <?php if (isset($errors['posting_mode'])): ?>
                            <p class="field__error" id="posting-mode-error"><?= e($errors['posting_mode']) ?></p>
                        <?php endif; ?>
                    </fieldset>

                    <!-- user_groups.default_post_limit -->
                    <div class="field">
                        <label class="field__label" for="post-limit">Antal opslag pr. oplevelse</label>
                        <input class="field__input field__input--short" id="post-limit" name="default_post_limit"
                            type="number" min="1" max="50" required
                            value="<?= e((string) ($old['default_post_limit'] ?? '20')) ?>"
                            <?php if (isset($errors['default_post_limit'])): ?>aria-invalid="true"
                            aria-describedby="post-limit-hint post-limit-error"
                            <?php else: ?>aria-describedby="post-limit-hint" <?php endif; ?>>
                        <p class="meta" id="post-limit-hint">Gælder nye oplevelser. Oplevelser, der allerede er i gang,
                            beholder deres antal.</p>
                        <?php if (isset($errors['default_post_limit'])): ?>
                            <p class="field__error" id="post-limit-error"><?= e($errors['default_post_limit']) ?></p>
                        <?php endif; ?>
                    </div>

                    <!-- user_groups.style is not in the form: no page uses it yet -->

                    <div class="button-row">
                        <button class="button button--primary" type="submit">Gem indstillinger</button>
                        <a class="button button--secondary"
                            href="<?= url('/group?id=' . $group['group_id']) ?>">Annuller</a>
                    </div>
                </form>
            </section>
        </div>
    </div>
</main>