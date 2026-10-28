<?php

/** @var string $state 'invalid' | 'guest' | 'member' | 'join' */
/** @var bool $isLoggedIn */
/** @var array|null $invitation */
/** @var int|null $memberCount */
/** @var string|null $token */
?>

<!-- The page an invitation link opens: /join?token=…
     Four states (decided in GroupController::join):
     invalid: the link is unknown, used or expired: only a message, nothing about the group.
     guest:   not logged in: create a user or log in first; the token is passed on.
     member:  already a member: a link to the group.
     join:    logged in: one button that makes the user a member. -->

<main id="main" class="site-main">
    <div class="container">
        <div class="envelope envelope--still">
            <div class="envelope__back" aria-hidden="true"></div>
            <div class="envelope__letter flow">
                <p class="eyebrow">Invitation</p>

                <?php if ($state === 'invalid'): ?>

                    <h1>Linket virker ikke</h1>
                    <p>Det er måske brugt eller udløbet. Bed den, der inviterede dig, om et nyt link.</p>
                    <?php if ($isLoggedIn): ?>
                        <p><a href="<?= url('/home') ?>">Til Mine grupper</a></p>
                    <?php else: ?>
                        <p><a href="<?= url('/') ?>">Til forsiden</a></p>
                    <?php endif; ?>

                <?php elseif ($state === 'member'): ?>
                    <h1>Du er allerede medlem af <?= e($invitation['name']) ?></h1>
                    <p>Linket er ikke brugt, så den, det var tiltænkt, kan stadig bruge det.</p>
                    <p><a class="button button--primary" href="<?= url('/group?id=' . $invitation['group_id']) ?>">Gå til
                            gruppen</a></p>

                <?php else: ?>
                    <h1>Du er inviteret til <?= e($invitation['name']) ?></h1>
                    <p>Fra <strong><?= e($invitation['first_name'] . ' ' . $invitation['last_name']) ?></strong>.</p>
                    <?php if ($invitation['description'] !== null): ?>
                        <p><?= e($invitation['description']) ?></p>
                    <?php endif; ?>
                    <p class="meta"><?= $memberCount === 1 ? '1 medlem' : $memberCount . ' medlemmer' ?></p>

                    <!-- Answers "Who can see this?" before the user joins -->
                    <p class="alert">Det, du deler i gruppen, kan kun gruppens medlemmer se. Andre grupper ser det aldrig.
                    </p>

                    <?php if ($state === 'guest'): ?>
                        <div class="form__actions">
                            <a class="button button--primary" href="<?= url('/register?invite=' . urlencode($token)) ?>">Opret
                                bruger og bliv medlem</a>
                            <p>Har du allerede en bruger? <a href="<?= url('/login?invite=' . urlencode($token)) ?>">Log ind</a>
                            </p>
                        </div>
                    <?php else: ?>
                        <form class="form__actions" action="<?= url('/join') ?>" method="post">
                            <?= \PostCard\Core\Csrf::field() ?>
                            <input type="hidden" name="token" value="<?= e($token) ?>">
                            <button class="button button--primary" type="submit">Bliv medlem</button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="envelope__front" aria-hidden="true"></div>
        </div>
    </div>
</main>