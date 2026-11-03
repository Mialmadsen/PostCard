<?php
// shows user where he is -breadcrumds
?>
<nav class="breadcrumb" aria-label="Du er her">
    <ol class="breadcrumb__list">
        <?php foreach ($breadcrumbs as $index => $crumb): ?>
        <?php
            // The last crumb is the page we are on
            $isCurrent = $index === array_key_last($breadcrumbs);
            ?>
        <li class="breadcrumb__item">
            <a class="breadcrumb__link" href="<?= url($crumb['path']) ?>" <?php if ($isCurrent): ?>aria-current="page"
                <?php endif; ?>><?= e($crumb['label']) ?>
            </a>
        </li>
        <?php endforeach; ?>
    </ol>
</nav>