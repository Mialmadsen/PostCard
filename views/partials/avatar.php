<?php

/**
 * Round avatar: the profile picture if there is one, otherwise the initials.
 * Set these two before the require:
 *   $avatarUser  – a users row with user_id, first_name, last_name, profile_image, profile_image_version
 *   $avatarClass – extra classes, e.g. 'avatar--large', or ''
 * alt="" because the name always stands next to the avatar.
 */

?>
<?php if ($avatarUser['profile_image'] !== null): ?>
    <?php /* ?v= changes with every new picture, so the browser fetches the new one instead of its saved copy */ ?>
    <img class="avatar <?= e($avatarClass) ?>"
        src="<?= url('/profile-image?user_id=' . $avatarUser['user_id'] . '&v=' . $avatarUser['profile_image_version']) ?>"
        alt="">
<?php else: ?>
    <span class="avatar <?= e($avatarClass) ?>"
        aria-hidden="true"><?= e(initials($avatarUser['first_name'], $avatarUser['last_name'])) ?></span>
<?php endif; ?>
