<?php
$pageTitle = 'Administration · Opslag';
$isOperator = true;
$pageStyle = 'admin';
$pageScripts = ['confirm'];
$adminPage = 'posts';
require __DIR__ . '/../views/partials/header.php';
?>

<!-- The operator can see posts from every group, only to remove posts that break the rules.
     Every page view here should be needed for moderation, not for browsing. -->

<main id="main" class="site-main">
    <div class="container">
        <p class="eyebrow">Administration</p>
        <h1>Opslag</h1>

        <div class="sidebar-layout">
            <?php require __DIR__ . '/../views/partials/admin-nav.php'; ?>

            <section class="card flow" aria-labelledby="posts-heading">
                <h2 class="visually-hidden" id="posts-heading">Alle opslag</h2>

                <!-- GET: searching changes nothing. admin-posts.php?user=1 (from admin-user.php) shows one user's posts. -->
                <form class="filter-bar" action="admin-posts.php" method="get" role="search">
                    <div class="field">
                        <label class="field__label" for="search">Søg i sted og tekst</label>
                        <input class="field__input" id="search" name="q" type="search">
                    </div>
                    <div class="field">
                        <label class="field__label" for="group">Gruppe</label>
                        <select class="field__input" id="group" name="group">
                            <option value="">Alle grupper</option>
                            <!-- LOOP: user_groups by name -->
                            <option value="1">Familien Nielsen</option>
                            <option value="2">Klasse 3.B</option>
                            <option value="3">Løbeklubben</option>
                        </select>
                    </div>
                    <button class="button button--secondary" type="submit">Søg</button>
                </form>

                <p class="meta">1.926 opslag</p>

                <div class="table-wrap" tabindex="0" role="region" aria-labelledby="posts-caption">
                    <table class="data-table">
                        <caption class="visually-hidden" id="posts-caption">Opslag, nyeste først</caption>
                        <thead>
                            <tr>
                                <th scope="col">Opslag</th>
                                <th scope="col">Gruppe</th>
                                <th scope="col">Forfatter</th>
                                <th scope="col">Dato</th>
                                <th scope="col">Billeder</th>
                                <th scope="col"><span class="visually-hidden">Handling</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- LOOP: posts matching the search, newest first (posts.created_at), 50 per page.
                                 Author = experiences.user_id. Slet removes the post, its images, likes, comments and views.
                                 A CSRF token is added in Epic 2. -->
                            <tr>
                                <td><a href="post.php?id=6">Prækestolen, Norge</a></td>
                                <td>Familien Nielsen</td>
                                <td><a href="admin-user.php?id=1">Mia Nielsen</a></td>
                                <td><time datetime="2026-09-30">30. sep. 2026</time></td>
                                <td>3</td>
                                <td>
                                    <form class="js-confirm" action="admin-posts.php" method="post"
                                        data-confirm="Vil du slette opslaget Prækestolen, Norge? Det kan ikke fortrydes.">
                                        <input type="hidden" name="post_id" value="6">
                                        <button class="button button--secondary" type="submit">Slet <span class="visually-hidden">Prækestolen, Norge</span></button>
                                    </form>
                                </td>
                            </tr>
                            <tr>
                                <td><a href="post.php?id=2">Isle of Skye, Skotland</a></td>
                                <td>Familien Nielsen</td>
                                <td><a href="admin-user.php?id=3">Anna Nielsen</a></td>
                                <td><time datetime="2026-09-28">28. sep. 2026</time></td>
                                <td>4</td>
                                <td>
                                    <form class="js-confirm" action="admin-posts.php" method="post"
                                        data-confirm="Vil du slette opslaget Isle of Skye, Skotland? Det kan ikke fortrydes.">
                                        <input type="hidden" name="post_id" value="2">
                                        <button class="button button--secondary" type="submit">Slet <span class="visually-hidden">Isle of Skye, Skotland</span></button>
                                    </form>
                                </td>
                            </tr>
                            <tr>
                                <td><a href="post.php?id=3">Hardangervidda, Norge</a></td>
                                <td>Løbeklubben</td>
                                <td><a href="admin-user.php?id=25">Jonas Berg</a></td>
                                <td><time datetime="2026-09-25">25. sep. 2026</time></td>
                                <td>2</td>
                                <td>
                                    <form class="js-confirm" action="admin-posts.php" method="post"
                                        data-confirm="Vil du slette opslaget Hardangervidda, Norge? Det kan ikke fortrydes.">
                                        <input type="hidden" name="post_id" value="3">
                                        <button class="button button--secondary" type="submit">Slet <span class="visually-hidden">Hardangervidda, Norge</span></button>
                                    </form>
                                </td>
                            </tr>
                            <tr>
                                <td><a href="post.php?id=4">Kystklipperne, Californien</a></td>
                                <td>Klasse 3.B</td>
                                <td><a href="admin-user.php?id=31">Sara Holm</a></td>
                                <td><time datetime="2026-09-24">24. sep. 2026</time></td>
                                <td>5</td>
                                <td>
                                    <form class="js-confirm" action="admin-posts.php" method="post"
                                        data-confirm="Vil du slette opslaget Kystklipperne, Californien? Det kan ikke fortrydes.">
                                        <input type="hidden" name="post_id" value="4">
                                        <button class="button button--secondary" type="submit">Slet <span class="visually-hidden">Kystklipperne, Californien</span></button>
                                    </form>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- IF more than 50 results:
                <nav aria-label="Sider"><a href="admin-posts.php?page=2">Næste side</a></nav> -->
            </section>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../views/partials/footer.php'; ?>
