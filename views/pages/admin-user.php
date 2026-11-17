<?php
// The controller shows this page with:
// View::render('admin-user', ['pageTitle' => 'Administration · Mia Nielsen', 'isOperator' => true, 'pageStyle' => 'admin', 'adminPage' => 'users', 'pageScripts' => ['confirm'], 'user' => [...], 'groups' => [...], 'usersMessage' => null, 'usersError' => null, 'errors' => [], 'old' => [...]]);

$userId = (int) $user['user_id'];
$fullName = $user['first_name'] . ' ' . $user['last_name'];

// The database's status words, as the operator reads them
$statusLabels = [
    'active' => 'Aktiv',
    'blocked' => 'Blokeret',
    'deleted' => 'Slettet',
];

// group_members.member_role and member_status, in words
$roleLabels = [
    'group_admin' => 'Admin',
    'member' => 'Medlem',
];
$memberStatusLabels = [
    'left' => 'har forladt gruppen',
    'removed' => 'fjernet fra gruppen',
];
?>

<main id="main" class="site-main">
    <div class="container">
        <nav class="breadcrumb" aria-label="Du er her">
            <ol class="breadcrumb__list">
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="<?= url('/admin') ?>">Administration</a></li>
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="<?= url('/admin/users') ?>">Brugere</a></li>
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="<?= url('/admin/user?id=' . $userId) ?>" aria-current="page"><?= e($fullName) ?></a></li>
            </ol>
        </nav>

        <header class="page-header">
            <p class="eyebrow">Administration · <?= (int) $user['is_operator'] === 1 ? 'operatør' : 'bruger' ?></p>
            <h1><?= e($fullName) ?></h1>
            <?php /* Status in words, not only colour. email_verified_at is NULL until the user clicks the link in the mail */ ?>
            <p class="meta">
                Oprettet <time datetime="<?= e(substr($user['created_at'], 0, 10)) ?>"><?= e(danishDate($user['created_at'])) ?></time>
                · <?= e($statusLabels[$user['user_status']]) ?>
                · <?= $user['email_verified_at'] !== null ? 'e-mail bekræftet' : 'e-mail ikke bekræftet' ?>
                · <?= (int) $user['group_count'] ?> <?= (int) $user['group_count'] === 1 ? 'gruppe' : 'grupper' ?>
                · <?= (int) $user['post_count'] ?> opslag
            </p>
        </header>

        <?php /* After Blokér / Fjern blokering: the seagull pop-up, success or error */ ?>
        <?php if ($usersMessage !== null): ?>
            <?php
            $flashMessage = $usersMessage;
            $flashType = 'success';
            $flashLink = null;
            require __DIR__ . '/../partials/flash.php';
            ?>
        <?php endif; ?>
        <?php if ($usersError !== null): ?>
            <?php
            $flashMessage = $usersError;
            $flashType = 'error';
            $flashLink = null;
            require __DIR__ . '/../partials/flash.php';
            ?>
        <?php endif; ?>

        <div class="sidebar-layout">
            <?php require __DIR__ . '/../partials/admin-nav.php'; ?>

            <div class="flow">
                <section class="card" aria-labelledby="profile-heading">
                    <h2 class="section-title" id="profile-heading">Profil</h2>

                    <?php if ((int) $user['is_operator'] === 1): ?>
                        <?php /* AdminUserController::update() refuses operators with 403, so no form here */ ?>
                        <p>En operatør ændrer selv sine oplysninger under Indstillinger.</p>
                    <?php else: ?>
                        <?php /* Only when the user has a picture: the operator sees it and can remove an unsuitable one.
                             Posts to AdminUserController::removePicture(); the initials are shown instead. */ ?>
                        <?php if ($user['profile_image'] !== null): ?>
                            <form class="js-confirm flow" action="<?= url('/admin/user/picture/delete') ?>" method="post"
                                data-confirm="Vil du fjerne profilbilledet for <?= e($fullName) ?>? Brugerens initialer vises i stedet."
                                data-confirm-button="Fjern billede">
                                <?php
                                $avatarUser = $user;
                                $avatarClass = 'avatar--large';
                                require __DIR__ . '/../partials/avatar.php';
                                ?>
                                <?= \PostCard\Core\Csrf::field() ?>
                                <input type="hidden" name="user_id" value="<?= $userId ?>">
                                <button class="button button--secondary" type="submit">Fjern profilbilledet</button>
                            </form>
                        <?php endif; ?>

                        <?php if ($errors !== []): ?>
                            <p class="alert alert--error" role="alert">Noget mangler eller er forkert. Se de markerede felter nedenfor.</p>
                        <?php endif; ?>

                        <?php /* The same fields the user has on settings.php. $old = the saved row, or what was typed after an error.
                             autocomplete="off": the browser must not fill in the operator's own name and e-mail */ ?>
                        <form class="form" action="<?= url('/admin/user') ?>" method="post" autocomplete="off">
                            <?= \PostCard\Core\Csrf::field() ?>
                            <input type="hidden" name="user_id" value="<?= $userId ?>">

                            <div class="field-row">
                                <div class="field">
                                    <label class="field__label" for="first-name">Fornavn</label>
                                    <input class="field__input" id="first-name" name="first_name" type="text"
                                        maxlength="100" required
                                        value="<?= e($old['first_name'] ?? '') ?>"
                                        <?php if (isset($errors['first_name'])): ?>aria-invalid="true" aria-describedby="first-name-error"
                                        <?php endif; ?>>
                                    <?php if (isset($errors['first_name'])): ?>
                                        <p class="field__error" id="first-name-error"><?= e($errors['first_name']) ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="field">
                                    <label class="field__label" for="last-name">Efternavn</label>
                                    <input class="field__input" id="last-name" name="last_name" type="text"
                                        maxlength="100" required
                                        value="<?= e($old['last_name'] ?? '') ?>"
                                        <?php if (isset($errors['last_name'])): ?>aria-invalid="true" aria-describedby="last-name-error"
                                        <?php endif; ?>>
                                    <?php if (isset($errors['last_name'])): ?>
                                        <p class="field__error" id="last-name-error"><?= e($errors['last_name']) ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="field">
                                <label class="field__label" for="username">Brugernavn</label>
                                <input class="field__input" id="username" name="username" type="text"
                                    maxlength="50" required
                                    value="<?= e($old['username'] ?? '') ?>"
                                    <?php if (isset($errors['username'])): ?>aria-invalid="true" aria-describedby="username-error"
                                    <?php endif; ?>>
                                <?php if (isset($errors['username'])): ?>
                                    <p class="field__error" id="username-error"><?= e($errors['username']) ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="field">
                                <label class="field__label" for="email">E-mail</label>
                                <input class="field__input" id="email" name="email" type="email"
                                    maxlength="255" required
                                    value="<?= e($old['email'] ?? '') ?>"
                                    <?php if (isset($errors['email'])): ?>aria-invalid="true"
                                    aria-describedby="email-hint email-error"
                                    <?php else: ?>aria-describedby="email-hint" <?php endif; ?>>
                                <?php /* On a new e-mail: email_verified_at becomes NULL (AdminUserController::update) */ ?>
                                <p class="meta" id="email-hint">Ændres e-mailen, er den nye adresse ikke bekræftet, før brugeren selv bekræfter den.</p>
                                <?php if (isset($errors['email'])): ?>
                                    <p class="field__error" id="email-error"><?= e($errors['email']) ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="field">
                                <label class="field__label" for="birthdate">Fødselsdato</label>
                                <input class="field__input" id="birthdate" name="birthdate" type="date"
                                    required
                                    value="<?= e($old['birthdate'] ?? '') ?>"
                                    <?php if (isset($errors['birthdate'])): ?>aria-invalid="true" aria-describedby="birthdate-error"
                                    <?php endif; ?>>
                                <?php if (isset($errors['birthdate'])): ?>
                                    <p class="field__error" id="birthdate-error"><?= e($errors['birthdate']) ?></p>
                                <?php endif; ?>
                            </div>

                            <div class="button-row">
                                <button class="button button--primary" type="submit">Gem profil</button>
                            </div>
                        </form>
                    <?php endif; ?>
                </section>

                <section class="card" aria-labelledby="status-heading">
                    <h2 class="section-title" id="status-heading">Status</h2>

                    <?php /* The same buttons as in the user list, posted to /admin/users.
                         from=user sends the operator back to this page afterwards (AdminUserController::updateStatus). */ ?>
                    <?php if ((int) $user['is_operator'] === 1): ?>
                        <p>Operatør. En operatør kan ikke blokeres.</p>
                    <?php elseif ($user['user_status'] === 'active'): ?>
                        <p>Aktiv: kan logge ind og bruge sine grupper.</p>
                        <form class="js-confirm" action="<?= url('/admin/users') ?>" method="post"
                            data-confirm="Vil du blokere <?= e($fullName) ?>? Brugeren bliver logget ud og kan ikke logge ind, før du fjerner blokeringen."
                            data-confirm-button="Blokér">
                            <?= \PostCard\Core\Csrf::field() ?>
                            <input type="hidden" name="user_id" value="<?= $userId ?>">
                            <input type="hidden" name="action" value="block">
                            <input type="hidden" name="from" value="user">
                            <button class="button button--secondary" type="submit">Blokér brugeren</button>
                        </form>
                    <?php elseif ($user['user_status'] === 'blocked'): ?>
                        <p>Blokeret: kan ikke logge ind. Opslag og kommentarer bliver i grupperne.</p>
                        <form action="<?= url('/admin/users') ?>" method="post">
                            <?= \PostCard\Core\Csrf::field() ?>
                            <input type="hidden" name="user_id" value="<?= $userId ?>">
                            <input type="hidden" name="action" value="unblock">
                            <input type="hidden" name="from" value="user">
                            <button class="button button--secondary" type="submit">Fjern blokering</button>
                        </form>
                    <?php else: ?>
                        <p>Slettet: kan ikke logge ind.</p>
                    <?php endif; ?>
                </section>

                <section class="card flow" aria-labelledby="password-heading">
                    <h2 id="password-heading">Adgangskode</h2>
                    <p>Operatører kan ikke se eller vælge en brugers adgangskode. Send i stedet et link, så brugeren selv kan vælge en ny.</p>

                    <?php /* Makes a reset_password token (user_tokens) and e-mails the link. */ ?>
                    <form action="<?= url('/admin/user') ?>" method="post">
                        <?= \PostCard\Core\Csrf::field() ?>
                        <input type="hidden" name="user_id" value="1">
                        <input type="hidden" name="form" value="reset-password">
                        <button class="button button--secondary" type="submit">Send link til ny adgangskode</button>
                    </form>
                </section>

                <section class="card" aria-labelledby="groups-heading">
                    <h2 class="section-title" id="groups-heading">Grupper</h2>

                    <?php if ($groups === []): ?>
                        <p class="meta">Ikke medlem af nogen grupper.</p>
                    <?php else: ?>
                        <?php /* Every group they are or were in, newest first. Each name opens the operator's view of that group */ ?>
                        <ul class="divided-list">
                            <?php foreach ($groups as $group): ?>
                                <li>
                                    <p><a href="<?= url('/admin/groups/show?id=' . (int) $group['group_id']) ?>"><?= e($group['name']) ?></a></p>
                                    <p class="meta">
                                        <?= e($roleLabels[$group['member_role']]) ?>
                                        <?php /* Left or removed: said in words, so the operator never mistakes it for a current membership */ ?>
                                        <?php if ($group['member_status'] !== 'active'): ?>
                                            · <?= e($memberStatusLabels[$group['member_status']]) ?>
                                        <?php endif; ?>
                                        · medlem siden <time datetime="<?= e(substr($group['joined_at'], 0, 10)) ?>"><?= e(danishDate($group['joined_at'])) ?></time>
                                        · <?= (int) $group['post_count'] ?> opslag
                                    </p>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </section>
            </div>
        </div>
    </div>
</main>
