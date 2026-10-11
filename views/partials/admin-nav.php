<?php
/**
 * Menu for the operator pages (admin-*.php).
 *
 * The page sets $adminPage BEFORE it requires this file, to mark where the operator is:
 *   'overview' | 'users' | 'posts' | 'content' | 'settings'
 */
$adminPage = $adminPage ?? '';

$adminLinks = [
    'overview' => ['admin.php', 'Oversigt'],
    'users'    => ['admin-users.php', 'Brugere'],
    'posts'    => ['admin-posts.php', 'Opslag'],
    'content'  => ['admin-content.php', 'Indhold'],
    'settings' => ['admin-settings.php', 'Indstillinger'],
];
?>
<nav class="card admin-nav" aria-label="Administration">
    <ul class="admin-nav__list">
        <?php foreach ($adminLinks as $key => [$href, $label]): ?>
            <li>
                <a class="admin-nav__link" href="<?= $href ?>"<?= $key === $adminPage ? ' aria-current="page"' : '' ?>><?= $label ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
