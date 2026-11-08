<?php

/**
 * A message after a redirect ("Dine oplysninger er gemt.") as a pop-up with the seagull postman and an OK button.
 * Set these before the require:
 *   $flashMessage – the text
 *   $flashType    – 'success' (happy seagull) or 'error' (confused seagull)
 *   $flashLink    – optional next step, e.g. ['label' => 'Gå til gruppen', 'path' => '/group?id=3'], or null
 * Without JavaScript the <dialog open> sits in the page, and OK still closes it (method="dialog").
 * flash-dialog.js turns it into a real pop-up: the page behind is dimmed, Escape and OK close it.
 */

$flashImage = $flashType === 'success' ? 'success-postman.svg' : 'error-postman.svg';

?>
<dialog class="dialog dialog--<?= e($flashType) ?> js-flash-dialog" open aria-labelledby="flash-dialog-text">
    <?php /* Decorative: the text says what happened */ ?>
    <img class="dialog__illustration" src="<?= asset('assets/images/illustrations/' . $flashImage) ?>" alt="">
    <p class="dialog__text" id="flash-dialog-text"><?= e($flashMessage) ?></p>

    <form class="button-row dialog__actions" method="dialog">
        <?php if ($flashLink !== null): ?>
            <a class="button button--secondary" href="<?= url($flashLink['path']) ?>"><?= e($flashLink['label']) ?></a>
        <?php endif; ?>
        <button class="button button--primary js-flash-dialog-ok" type="submit">OK</button>
    </form>
</dialog>
<script src="<?= asset('assets/js/flash-dialog.js') ?>" type="module"></script>
