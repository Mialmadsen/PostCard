<?php
$pageTitle = 'Medlemmer · Familien Nielsen';
$isLoggedIn = true;
$pageStyle = 'members';
$pageScripts = ['confirm'];
require __DIR__ . '/../views/partials/header.php';
?>

<main id="main" class="site-main">
    <div class="container container--text">
        <nav class="breadcrumb" aria-label="Du er her">
            <ol class="breadcrumb__list">
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="home.php">Mine grupper</a></li>
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="group.php?id=1">Familien Nielsen</a></li>
                <li class="breadcrumb__item"><a class="breadcrumb__link" href="members.php?group=1" aria-current="page">Medlemmer</a></li>
            </ol>
        </nav>

        <header class="page-header">
            <p class="group-label">Familien Nielsen</p>
            <h1>Medlemmer</h1>
            <p>13 medlemmer. Kun de kan se gruppens oplevelser og opslag.</p>
            <!-- IF member_role = 'group_admin' -->
            <p><a class="button button--secondary" href="invite.php?group=1">Inviter nye medlemmer</a></p>
            <!-- /IF -->
        </header>

        <div class="card-stack">
            <section class="card" aria-labelledby="members-heading">
                <h2 class="visually-hidden" id="members-heading">Alle medlemmer</h2>

                <!-- LOOP: group_members with member_status = 'active', admins first, then by first name.
                     Only admins see the buttons, and never on an admin's row. The server checks this again.
                     A CSRF token is added in Epic 2.
                     "Gør til admin": group_members.member_role = 'group_admin'.
                     "Fjern": group_members.member_status = 'removed'. -->
                <ul class="divided-list">
                    <li class="member">
                        <span class="avatar" aria-hidden="true">MN</span>
                        <div>
                            <p class="member__name">Mia Nielsen <span class="meta">(dig)</span></p>
                            <p class="meta">Admin · medlem siden <time datetime="2026-09-01">1. sep. 2026</time></p>
                        </div>
                    </li>
                    <li class="member">
                        <span class="avatar" aria-hidden="true">PN</span>
                        <div>
                            <p class="member__name">Peter Nielsen</p>
                            <p class="meta">Admin · medlem siden <time datetime="2026-09-01">1. sep. 2026</time></p>
                        </div>
                    </li>
                    <li class="member">
                        <span class="avatar" aria-hidden="true">AN</span>
                        <div>
                            <p class="member__name">Anna Nielsen</p>
                            <p class="meta">Medlem siden <time datetime="2026-09-02">2. sep. 2026</time></p>
                        </div>
                        <div class="button-row member__actions">
                            <form class="js-confirm" action="members.php" method="post"
                                data-confirm="Vil du gøre Anna Nielsen til admin? Admins kan godkende oplevelser, invitere og fjerne medlemmer.">
                                <input type="hidden" name="group_id" value="1">
                                <input type="hidden" name="user_id" value="3">
                                <input type="hidden" name="action" value="make-admin">
                                <button class="button button--secondary" type="submit">Gør <span class="visually-hidden">Anna Nielsen</span> til admin</button>
                            </form>
                            <form class="js-confirm" action="members.php" method="post"
                                data-confirm="Vil du fjerne Anna Nielsen fra Familien Nielsen? Det, der er delt, bliver i gruppens album.">
                                <input type="hidden" name="group_id" value="1">
                                <input type="hidden" name="user_id" value="3">
                                <input type="hidden" name="action" value="remove">
                                <button class="button button--secondary" type="submit">Fjern <span class="visually-hidden">Anna Nielsen</span></button>
                            </form>
                        </div>
                    </li>
                    <li class="member">
                        <span class="avatar" aria-hidden="true">BL</span>
                        <div>
                            <p class="member__name">Bo Larsen</p>
                            <p class="meta">Medlem siden <time datetime="2026-09-05">5. sep. 2026</time></p>
                        </div>
                        <div class="button-row member__actions">
                            <form class="js-confirm" action="members.php" method="post"
                                data-confirm="Vil du gøre Bo Larsen til admin? Admins kan godkende oplevelser, invitere og fjerne medlemmer.">
                                <input type="hidden" name="group_id" value="1">
                                <input type="hidden" name="user_id" value="10">
                                <input type="hidden" name="action" value="make-admin">
                                <button class="button button--secondary" type="submit">Gør <span class="visually-hidden">Bo Larsen</span> til admin</button>
                            </form>
                            <form class="js-confirm" action="members.php" method="post"
                                data-confirm="Vil du fjerne Bo Larsen fra Familien Nielsen? Det, der er delt, bliver i gruppens album.">
                                <input type="hidden" name="group_id" value="1">
                                <input type="hidden" name="user_id" value="10">
                                <input type="hidden" name="action" value="remove">
                                <button class="button button--secondary" type="submit">Fjern <span class="visually-hidden">Bo Larsen</span></button>
                            </form>
                        </div>
                    </li>
                    <li class="member">
                        <span class="avatar" aria-hidden="true">EN</span>
                        <div>
                            <p class="member__name">Emma Nielsen</p>
                            <p class="meta">Medlem siden <time datetime="2026-09-03">3. sep. 2026</time></p>
                        </div>
                        <div class="button-row member__actions">
                            <form class="js-confirm" action="members.php" method="post"
                                data-confirm="Vil du gøre Emma Nielsen til admin? Admins kan godkende oplevelser, invitere og fjerne medlemmer.">
                                <input type="hidden" name="group_id" value="1">
                                <input type="hidden" name="user_id" value="7">
                                <input type="hidden" name="action" value="make-admin">
                                <button class="button button--secondary" type="submit">Gør <span class="visually-hidden">Emma Nielsen</span> til admin</button>
                            </form>
                            <form class="js-confirm" action="members.php" method="post"
                                data-confirm="Vil du fjerne Emma Nielsen fra Familien Nielsen? Det, der er delt, bliver i gruppens album.">
                                <input type="hidden" name="group_id" value="1">
                                <input type="hidden" name="user_id" value="7">
                                <input type="hidden" name="action" value="remove">
                                <button class="button button--secondary" type="submit">Fjern <span class="visually-hidden">Emma Nielsen</span></button>
                            </form>
                        </div>
                    </li>
                    <li class="member">
                        <span class="avatar" aria-hidden="true">FN</span>
                        <div>
                            <p class="member__name">Freja Nielsen</p>
                            <p class="meta">Medlem siden <time datetime="2026-09-06">6. sep. 2026</time></p>
                        </div>
                        <div class="button-row member__actions">
                            <form class="js-confirm" action="members.php" method="post"
                                data-confirm="Vil du gøre Freja Nielsen til admin? Admins kan godkende oplevelser, invitere og fjerne medlemmer.">
                                <input type="hidden" name="group_id" value="1">
                                <input type="hidden" name="user_id" value="11">
                                <input type="hidden" name="action" value="make-admin">
                                <button class="button button--secondary" type="submit">Gør <span class="visually-hidden">Freja Nielsen</span> til admin</button>
                            </form>
                            <form class="js-confirm" action="members.php" method="post"
                                data-confirm="Vil du fjerne Freja Nielsen fra Familien Nielsen? Det, der er delt, bliver i gruppens album.">
                                <input type="hidden" name="group_id" value="1">
                                <input type="hidden" name="user_id" value="11">
                                <input type="hidden" name="action" value="remove">
                                <button class="button button--secondary" type="submit">Fjern <span class="visually-hidden">Freja Nielsen</span></button>
                            </form>
                        </div>
                    </li>
                    <li class="member">
                        <span class="avatar" aria-hidden="true">IL</span>
                        <div>
                            <p class="member__name">Ida Larsen</p>
                            <p class="meta">Medlem siden <time datetime="2026-09-05">5. sep. 2026</time></p>
                        </div>
                        <div class="button-row member__actions">
                            <form class="js-confirm" action="members.php" method="post"
                                data-confirm="Vil du gøre Ida Larsen til admin? Admins kan godkende oplevelser, invitere og fjerne medlemmer.">
                                <input type="hidden" name="group_id" value="1">
                                <input type="hidden" name="user_id" value="9">
                                <input type="hidden" name="action" value="make-admin">
                                <button class="button button--secondary" type="submit">Gør <span class="visually-hidden">Ida Larsen</span> til admin</button>
                            </form>
                            <form class="js-confirm" action="members.php" method="post"
                                data-confirm="Vil du fjerne Ida Larsen fra Familien Nielsen? Det, der er delt, bliver i gruppens album.">
                                <input type="hidden" name="group_id" value="1">
                                <input type="hidden" name="user_id" value="9">
                                <input type="hidden" name="action" value="remove">
                                <button class="button button--secondary" type="submit">Fjern <span class="visually-hidden">Ida Larsen</span></button>
                            </form>
                        </div>
                    </li>
                    <li class="member">
                        <span class="avatar" aria-hidden="true">JN</span>
                        <div>
                            <p class="member__name">Jens Nielsen</p>
                            <p class="meta">Medlem siden <time datetime="2026-09-02">2. sep. 2026</time></p>
                        </div>
                        <div class="button-row member__actions">
                            <form class="js-confirm" action="members.php" method="post"
                                data-confirm="Vil du gøre Jens Nielsen til admin? Admins kan godkende oplevelser, invitere og fjerne medlemmer.">
                                <input type="hidden" name="group_id" value="1">
                                <input type="hidden" name="user_id" value="5">
                                <input type="hidden" name="action" value="make-admin">
                                <button class="button button--secondary" type="submit">Gør <span class="visually-hidden">Jens Nielsen</span> til admin</button>
                            </form>
                            <form class="js-confirm" action="members.php" method="post"
                                data-confirm="Vil du fjerne Jens Nielsen fra Familien Nielsen? Det, der er delt, bliver i gruppens album.">
                                <input type="hidden" name="group_id" value="1">
                                <input type="hidden" name="user_id" value="5">
                                <input type="hidden" name="action" value="remove">
                                <button class="button button--secondary" type="submit">Fjern <span class="visually-hidden">Jens Nielsen</span></button>
                            </form>
                        </div>
                    </li>
                    <li class="member">
                        <span class="avatar" aria-hidden="true">KN</span>
                        <div>
                            <p class="member__name">Karen Nielsen</p>
                            <p class="meta">Medlem siden <time datetime="2026-09-02">2. sep. 2026</time></p>
                        </div>
                        <div class="button-row member__actions">
                            <form class="js-confirm" action="members.php" method="post"
                                data-confirm="Vil du gøre Karen Nielsen til admin? Admins kan godkende oplevelser, invitere og fjerne medlemmer.">
                                <input type="hidden" name="group_id" value="1">
                                <input type="hidden" name="user_id" value="6">
                                <input type="hidden" name="action" value="make-admin">
                                <button class="button button--secondary" type="submit">Gør <span class="visually-hidden">Karen Nielsen</span> til admin</button>
                            </form>
                            <form class="js-confirm" action="members.php" method="post"
                                data-confirm="Vil du fjerne Karen Nielsen fra Familien Nielsen? Det, der er delt, bliver i gruppens album.">
                                <input type="hidden" name="group_id" value="1">
                                <input type="hidden" name="user_id" value="6">
                                <input type="hidden" name="action" value="remove">
                                <button class="button button--secondary" type="submit">Fjern <span class="visually-hidden">Karen Nielsen</span></button>
                            </form>
                        </div>
                    </li>
                    <li class="member">
                        <span class="avatar" aria-hidden="true">LN</span>
                        <div>
                            <p class="member__name">Lise Nielsen</p>
                            <p class="meta">Medlem siden <time datetime="2026-09-02">2. sep. 2026</time></p>
                        </div>
                        <div class="button-row member__actions">
                            <form class="js-confirm" action="members.php" method="post"
                                data-confirm="Vil du gøre Lise Nielsen til admin? Admins kan godkende oplevelser, invitere og fjerne medlemmer.">
                                <input type="hidden" name="group_id" value="1">
                                <input type="hidden" name="user_id" value="4">
                                <input type="hidden" name="action" value="make-admin">
                                <button class="button button--secondary" type="submit">Gør <span class="visually-hidden">Lise Nielsen</span> til admin</button>
                            </form>
                            <form class="js-confirm" action="members.php" method="post"
                                data-confirm="Vil du fjerne Lise Nielsen fra Familien Nielsen? Det, der er delt, bliver i gruppens album.">
                                <input type="hidden" name="group_id" value="1">
                                <input type="hidden" name="user_id" value="4">
                                <input type="hidden" name="action" value="remove">
                                <button class="button button--secondary" type="submit">Fjern <span class="visually-hidden">Lise Nielsen</span></button>
                            </form>
                        </div>
                    </li>
                    <li class="member">
                        <span class="avatar" aria-hidden="true">MH</span>
                        <div>
                            <p class="member__name">Morten Holm</p>
                            <p class="meta">Medlem siden <time datetime="2026-09-12">12. sep. 2026</time></p>
                        </div>
                        <div class="button-row member__actions">
                            <form class="js-confirm" action="members.php" method="post"
                                data-confirm="Vil du gøre Morten Holm til admin? Admins kan godkende oplevelser, invitere og fjerne medlemmer.">
                                <input type="hidden" name="group_id" value="1">
                                <input type="hidden" name="user_id" value="12">
                                <input type="hidden" name="action" value="make-admin">
                                <button class="button button--secondary" type="submit">Gør <span class="visually-hidden">Morten Holm</span> til admin</button>
                            </form>
                            <form class="js-confirm" action="members.php" method="post"
                                data-confirm="Vil du fjerne Morten Holm fra Familien Nielsen? Det, der er delt, bliver i gruppens album.">
                                <input type="hidden" name="group_id" value="1">
                                <input type="hidden" name="user_id" value="12">
                                <input type="hidden" name="action" value="remove">
                                <button class="button button--secondary" type="submit">Fjern <span class="visually-hidden">Morten Holm</span></button>
                            </form>
                        </div>
                    </li>
                    <li class="member">
                        <span class="avatar" aria-hidden="true">ON</span>
                        <div>
                            <p class="member__name">Oliver Nielsen</p>
                            <p class="meta">Medlem siden <time datetime="2026-09-03">3. sep. 2026</time></p>
                        </div>
                        <div class="button-row member__actions">
                            <form class="js-confirm" action="members.php" method="post"
                                data-confirm="Vil du gøre Oliver Nielsen til admin? Admins kan godkende oplevelser, invitere og fjerne medlemmer.">
                                <input type="hidden" name="group_id" value="1">
                                <input type="hidden" name="user_id" value="8">
                                <input type="hidden" name="action" value="make-admin">
                                <button class="button button--secondary" type="submit">Gør <span class="visually-hidden">Oliver Nielsen</span> til admin</button>
                            </form>
                            <form class="js-confirm" action="members.php" method="post"
                                data-confirm="Vil du fjerne Oliver Nielsen fra Familien Nielsen? Det, der er delt, bliver i gruppens album.">
                                <input type="hidden" name="group_id" value="1">
                                <input type="hidden" name="user_id" value="8">
                                <input type="hidden" name="action" value="remove">
                                <button class="button button--secondary" type="submit">Fjern <span class="visually-hidden">Oliver Nielsen</span></button>
                            </form>
                        </div>
                    </li>
                    <li class="member">
                        <span class="avatar" aria-hidden="true">SM</span>
                        <div>
                            <p class="member__name">Søren Madsen</p>
                            <p class="meta">Medlem siden <time datetime="2026-09-04">4. sep. 2026</time></p>
                        </div>
                        <div class="button-row member__actions">
                            <form class="js-confirm" action="members.php" method="post"
                                data-confirm="Vil du gøre Søren Madsen til admin? Admins kan godkende oplevelser, invitere og fjerne medlemmer.">
                                <input type="hidden" name="group_id" value="1">
                                <input type="hidden" name="user_id" value="13">
                                <input type="hidden" name="action" value="make-admin">
                                <button class="button button--secondary" type="submit">Gør <span class="visually-hidden">Søren Madsen</span> til admin</button>
                            </form>
                            <form class="js-confirm" action="members.php" method="post"
                                data-confirm="Vil du fjerne Søren Madsen fra Familien Nielsen? Det, der er delt, bliver i gruppens album.">
                                <input type="hidden" name="group_id" value="1">
                                <input type="hidden" name="user_id" value="13">
                                <input type="hidden" name="action" value="remove">
                                <button class="button button--secondary" type="submit">Fjern <span class="visually-hidden">Søren Madsen</span></button>
                            </form>
                        </div>
                    </li>
                </ul>
                <!-- /LOOP -->
            </section>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../views/partials/footer.php'; ?>
