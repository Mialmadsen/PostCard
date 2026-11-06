
<?php
// Breadcrumbs viser, hvor brugeren befinder sig på hjemmesiden.
$breadcrumbs = [
    ['label' => 'Mine grupper', 'path' => '/home'],
    ['label' => $post['group_name'], 'path' => '/group?id=' . $post['group_id']],
    ['label' => $post['experience_name'], 'path' => '/experience?id=' . $post['experience_id']],
    ['label' => $post['location'] ?: 'Opslag', 'path' => '/post?id=' . $post['id']],
    ['label' => 'Rediger', 'path' => '/edit-post?id=' . $post['id']]
];
?>

<main id="main" class="site-main">
    <div class="container">
        <?php require __DIR__ . '/../partials/breadcrumbs.php'; ?>

        <header class="page-header">
            <p class="group-label">
                <?= e($post['group_name']) ?> · <?= e($post['experience_name']) ?>
            </p>
            <h1>Rediger opslag</h1>
        </header>

        <div class="sidebar-layout">
            <?php /* Viser hvilken gruppe og oplevelse opslaget tilhører. */ ?>
            <aside class="card flow" aria-labelledby="audience-heading">
                <h2 id="audience-heading">Til</h2>
                <dl class="address">
                    <div>
                        <dt class="group-label">Gruppe</dt>
                        <dd class="address__value"><?= e($post['group_name']) ?></dd>
                    </div>
                    <div>
                        <dt class="group-label">Oplevelse</dt>
                        <dd class="address__value"><?= e($post['experience_name']) ?></dd>
                    </div>
                </dl>
            </aside>

            <section class="card" aria-labelledby="form-heading">
                <h2 class="section-title" id="form-heading">Dit opslag</h2>

                <?php /* Formularen sender ændringerne til PostController::update(). */ ?>
                <form class="form" action="<?= e(url('/edit-post')) ?>" method="post" enctype="multipart/form-data">
                    <?= \PostCard\Core\Csrf::field() ?>
                    <input type="hidden" name="post_id" value="<?= (int) $post['id'] ?>">

                    <?php /* Billeder: eksisterende og nye billeder kan redigeres. */ ?>
                    <div class="field">
                        <label class="field__label" for="photos">Billeder</label>
                        <input class="field__input js-photo-input"
                            type="file" id="photos" name="photos[]"
                            accept="image/jpeg,image/png,image/webp" multiple>
                        <p class="meta">Du kan have mellem 1 og 5 billeder. JPG, PNG eller WebP, maks. 5 MB pr. billede.</p>
                        <?php /* JavaScript bruger listen til at vise alle billeder. */ ?>
                        <ul class="photo-preview js-photo-preview">
                            <?php /* Eksisterende billeder fra databasen. */ ?>
                            <?php foreach ($images as $image): ?>
                                <li class="polaroid photo-preview__item" data-image-id="<?= (int) $image['image_id'] ?>">
                                    <img class="polaroid__photo"
                                        src="<?= e(url('/post-image?id=' . (int) $image['image_id'])) ?>"
                                        alt="<?= e($image['alt_text'] ?? '') ?>" loading="lazy">
                                    <div class="photo-preview__controls">
                                        <label>
                                            <input type="radio" class="js-photo-cover" name="cover"
                                                value="saved-<?= (int) $image['image_id'] ?>">
                                            Forsidebillede
                                        </label>
                                        <details>
                                            <summary>Billedbeskrivelse</summary>
                                            <label class="field__label" for="saved-alt-<?= (int) $image['image_id'] ?>">Beskrivelse</label>
                                            <input class="field__input js-photo-alt"
                                                id="saved-alt-<?= (int) $image['image_id'] ?>"
                                                name="saved_alt_text[<?= (int) $image['image_id'] ?>]"
                                                value="<?= e($image['alt_text'] ?? '') ?>" maxlength="255">
                                        </details>
                                        <button type="button" class="js-photo-remove">
                                            Fjern <span class="visually-hidden">billede</span>
                                        </button>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php /* Viser antal valgte billeder og eventuelle fejl. */ ?>
                        <p class="meta js-photo-status" aria-live="polite"></p>
                        <?php /* Skabelon til nye og eksisterende billeder. */ ?>
                        <template class="js-photo-template">
                            <li class="polaroid photo-preview__item">
                                <img class="polaroid__photo" alt="">
                                <div class="photo-preview__controls">
                                    <div>
                                        <input type="radio" class="js-photo-cover" name="cover">
                                        <label class="js-photo-cover-label">Forsidebillede</label>
                                    </div>
                                    <details>
                                        <summary>Billedbeskrivelse</summary>
                                        <label class="field__label js-photo-alt-label">Beskrivelse</label>
                                        <input class="field__input js-photo-alt" type="text" maxlength="255">
                                    </details>
                                    <button type="button" class="js-photo-remove">
                                        Fjern <span class="visually-hidden">billede</span>
                                    </button>
                                </div>
                            </li>
                        </template>
                    </div>

                    <?php /* Brugeren kan ændre opslagets lokation. */ ?>
                    <div class="field">
                        <label class="field__label" for="location">Sted</label>
                        <input class="field__input" id="location" name="location"
                               type="text" maxlength="150"
                               value="<?= e($post['location'] ?? '') ?>"
                               aria-describedby="location-hint">
                        <p class="meta" id="location-hint">Fx Prækestolen, Norge eller hjemme i haven</p>
                    </div>

                    <?php /* Brugeren kan ændre opslagets tekst. */ ?>
                    <div class="field">
                        <label class="field__label" for="caption">Fortæl om det</label>
                        <textarea class="field__input" id="caption" name="caption"
                                  rows="5" maxlength="2000"
                                  aria-describedby="caption-hint"><?= e($post['caption'] ?? '') ?></textarea>
                        <p class="meta" id="caption-hint">Det, gruppen læser under billederne</p>
                    </div>

                    <p class="alert">
                        Ændringerne bliver vist med det samme for medlemmerne af
                        <strong><?= e($post['group_name']) ?></strong>.
                        Det tæller ikke som et nyt opslag.
                    </p>

                    <div class="button-row">
                        <button class="button button--primary" type="submit">Gem ændringer</button>
                        <a class="button button--secondary"
                           href="<?= e(url('/post?id=' . $post['id'])) ?>">Annuller</a>
                    </div>
                </form>
            </section>
        </div>
    </div>
</main>
