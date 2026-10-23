<?php
/**
 * Menu for the operator pages (/admin/...).
 *
 * The controller sends 'adminPage' to View::render, to mark where the operator is:
 *   'overview' | 'users' | 'posts' | 'content' | 'settings'
 */
$adminPage = $adminPage ?? '';

$adminLinks = [
    'overview' => ['/admin', 'Oversigt'],
    'users'    => ['/admin/users', 'Brugere'],
    'posts'    => ['/admin/posts', 'Opslag'],
    'content'  => ['/admin/content', 'Indhold'],
    'settings' => ['/admin/settings', 'Indstillinger'],
];
?>
<nav class="card admin-nav" aria-label="Administration">
    <ul class="admin-nav__list">
        <?php foreach ($adminLinks as $key => [$path, $label]): ?>
            <li>
                <a class="admin-nav__link" href="<?= url($path) ?>"<?= $key === $adminPage ? ' aria-current="page"' : '' ?>><?= $label ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
