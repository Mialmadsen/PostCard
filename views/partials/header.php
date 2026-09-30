<?php
/**
 * Shared header for every page.
 *
 * The page sets these variables BEFORE it requires this file:
 *   $pageTitle  – text for the browser tab (required)
 *   $isLoggedIn – true shows the member navigation, false the public one
 *   $pageStyle  – optional: name of a stylesheet in assets/css/pages/ (without .css)
 */
$pageTitle = $pageTitle ?? 'Del din oplevelse';
$isLoggedIn = $isLoggedIn ?? false;
$pageStyle = $pageStyle ?? null;
?>
<!DOCTYPE html>
<html lang="da">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> · PostCard</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Caveat:wght@400;600&family=Jost:wght@300;400;500;600&display=swap">

    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="stylesheet" href="assets/css/components/buttons.css">
    <link rel="stylesheet" href="assets/css/components/card.css">
    <link rel="stylesheet" href="assets/css/components/envelope.css">
    <link rel="stylesheet" href="assets/css/components/site-header.css">
    <link rel="stylesheet" href="assets/css/components/site-footer.css">

    <?php if ($pageStyle): ?>
        <link rel="stylesheet" href="assets/css/pages/<?= htmlspecialchars($pageStyle) ?>.css">
    <?php endif; ?>

    <?php if ($isLoggedIn): ?>
        <script src="assets/js/user-menu.js" defer></script>
    <?php endif; ?>
</head>

<body>
    <a class="skip-link" href="#main">Spring til indhold</a>

    <header class="site-header">
        <div class="site-header__inner container">

            <a class="logo" href="index.php" aria-label="PostCard">
                <svg class="logo__stamp" viewBox="0 0 36 44" aria-hidden="true" focusable="false">
                    <defs>
                        <!-- Perforation: a grid of holes, used only along the four edges -->
                        <pattern id="stamp-holes" x="-2" y="-2" width="4" height="4" patternUnits="userSpaceOnUse">
                            <circle cx="2" cy="2" r="1.5" fill="black" />
                        </pattern>
                        <mask id="logo-perforation">
                            <rect width="36" height="44" fill="white" />
                            <rect x="0" y="-2" width="36" height="4" fill="url(#stamp-holes)" />
                            <rect x="0" y="42" width="36" height="4" fill="url(#stamp-holes)" />
                            <rect x="-2" y="0" width="4" height="44" fill="url(#stamp-holes)" />
                            <rect x="34" y="0" width="4" height="44" fill="url(#stamp-holes)" />
                        </mask>
                    </defs>
                    <!-- Stamp paper with perforated edge -->
                    <rect class="logo__stamp-paper" width="36" height="44" mask="url(#logo-perforation)" />
                    <!-- Picture: sky, sun and sea -->
                    <rect class="logo__stamp-sky" x="5" y="5" width="26" height="34" />
                    <circle class="logo__stamp-sun" cx="23" cy="15" r="5" />
                    <path class="logo__stamp-wave" d="M5 27c3-3 5-3 8 0s5 3 8 0 5-3 8 0 2 1 2 1" />
                    <path class="logo__stamp-wave" d="M5 33c3-3 5-3 8 0s5 3 8 0 5-3 8 0 2 1 2 1" />
                </svg>
                <span class="logo__text">PostCard</span>
            </a>

            <nav class="site-nav" aria-label="Hovedmenu">
                <?php if ($isLoggedIn): ?>

                    <!-- NAV: logged in -->
                    <ul class="site-nav__list">
                        <li><a class="site-nav__link" href="home.php">Mine grupper</a></li>
                        <li class="user-menu">
                            <button class="user-menu__button js-user-menu-button" type="button"
                                aria-expanded="false" aria-controls="user-menu-list">
                                <span class="avatar" aria-hidden="true">MN</span>
                                <span class="user-menu__name">Mia</span>
                                <span class="visually-hidden">– åbn brugermenu</span>
                                <svg class="user-menu__chevron" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                    <path d="m6 9 6 6 6-6" />
                                </svg>
                            </button>
                            <ul class="user-menu__list" id="user-menu-list">
                                <li><a class="user-menu__item" href="profile.php">Min profil</a></li>
                                <li><a class="user-menu__item" href="settings.php">Indstillinger</a></li>
                                <li>
                                    <!-- Log out is a POST form (a CSRF token is added in Epic 2) -->
                                    <form action="logout.php" method="post">
                                        <button class="user-menu__item" type="submit">Log ud</button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>

                <?php else: ?>

                    <!-- NAV: logged out. "Opret bruger" is the main action on the page itself, not here -->
                    <ul class="site-nav__list">
                        <li><a class="site-nav__link" href="login.php">Log ind</a></li>
                    </ul>

                <?php endif; ?>
            </nav>

        </div>
    </header>
