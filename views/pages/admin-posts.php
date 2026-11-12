<?php
// Operatoren kan se alle opslag på PostCard.
// $posts kommer fra AdminPostController.
?>

<main id="main" class="site-main">
    <div class="container">
        <p class="eyebrow">Administration</p>
        <h1>Opslag</h1>

        <div class="sidebar-layout">
            <?php require __DIR__ . '/../partials/admin-nav.php'; ?>

            <section class="card flow" aria-labelledby="posts-heading">
                <h2 class="visually-hidden" id="posts-heading">Alle opslag</h2>

                
                <!-- Søgning og filtrering af opslag -->
                <form class="filter-bar" action="<?= e(url('/admin/posts')) ?>" method="get" role="search">

                <!-- Søg efter lokation, tekst eller oplevelse -->
                <div class="field">
                    <label class="field__label" for="search">
                        Søg i sted og tekst
                    </label>

                    <input
                        class="field__input"
                        id="search"
                        name="q"
                        type="search"
                        value="<?= e($search) ?>"
                        placeholder="Søg efter opslag..."
                    >
                </div>

                <!-- Filtrer opslag efter gruppe -->
                <div class="field">
                    <label class="field__label" for="group">
                        Gruppe
                    </label>

                    <select class="field__input" id="group" name="group">
                        <option value="">Alle grupper</option>

                        <?php foreach ($groups as $group): ?>
                            <option
                                value="<?= (int) $group['group_id'] ?>"
                                <?= $selectedGroupId === (int) $group['group_id'] ? 'selected' : '' ?>
                            >
                                <?= e($group['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Sender søgning og gruppefilter til controlleren -->
                <button class="button button--secondary" type="submit">
                    Søg
                </button>

                </form>


                <p class="meta"><?= count($posts) ?> opslag</p>

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
                                <th scope="col">
                                    <span class="visually-hidden">Handling</span>
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (empty($posts)): ?>
                                <tr>
                                    <td colspan="6">Ingen opslag fundet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($posts as $post): ?>
                                    <tr>
                                        
                                        <td>
                                            <a href="<?= e(url('/admin/posts/show?id=' . (int) $post['id'])) ?>">
                                                <?= e($post['location'] ?: $post['experience_name']) ?>
                                            </a>
                                        </td>

                                        <td><?= e($post['group_name']) ?></td>

                                        <td>
                                            <?= e($post['first_name'] . ' ' . $post['last_name']) ?>
                                        </td>

                                        <td>
                                            <time datetime="<?= e($post['created_at']) ?>">
                                                <?= date('d.m.Y', strtotime($post['created_at'])) ?>
                                            </time>
                                        </td>

                                        <td><?= (int) $post['image_count'] ?></td>

                                        <td>
                                            <!-- Kun operatorer kan bruge denne slettefunktion -->
                                            <form
                                                class="js-confirm"
                                                action="<?= e(url('/admin/posts/delete')) ?>"
                                                method="post"
                                                data-confirm="<?= e('Vil du slette opslaget fra ' . ($post['location'] ?: $post['experience_name']) . '? Det kan ikke fortrydes.') ?>"
                                                data-confirm-button="Ja, slet opslaget"
                                            >
                                                <!-- Beskytter formularen mod CSRF -->
                                                <?= \PostCard\Core\Csrf::field() ?>

                                                <!-- ID på opslaget, der skal slettes -->
                                                <input
                                                    type="hidden"
                                                    name="post_id"
                                                    value="<?= (int) $post['id'] ?>"
                                                >

                                                <button
                                                    class="button button--secondary"
                                                    type="submit"
                                                >
                                                    Slet
                                                    <span class="visually-hidden">
                                                        <?= e($post['location'] ?: $post['experience_name']) ?>
                                                    </span>
                                                </button>
                                            </form>
                                        </td>

                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </section>
        </div>
    </div>
</main>
