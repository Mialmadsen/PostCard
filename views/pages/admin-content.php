<?php
// The controller shows this page with:
// View::render('admin-content', ['pageTitle' => 'Administration · Indhold', 'isOperator' => true, 'pageStyle' => 'admin', 'adminPage' => 'content']);

/* The texts on the public pages (site_content). One form per text, so saving one
   does not touch the others. PHP escapes the text when it shows it, and turns
   an empty line into a new paragraph. */

// Operatoren kan redigere teksterne på PostCards offentlige sider.
// $content, $saved og $error kommer fra AdminContentController.

// De fire områder, der kan redigeres.
// Hver sektion har sin egen formular.

$sections = [
    'welcome' => [
        'label' => 'Forsiden: velkomst',
        'button' => 'Gem velkomst',
        'link' => '/'
    ],
    'rules' => [
        'label' => 'Regler',
        'button' => 'Gem regler',
        'link' => '/rules'
    ],
    'privacy' => [
        'label' => 'Regler: dine data',
        'button' => 'Gem dine data',
        'link' => '/rules'
    ],
    'contact' => [
        'label' => 'Kontakt',
        'button' => 'Gem kontakt',
        'link' => '/contact'
    ]
];
?>

<main id="main" class="site-main">
    <div class="container">

        <p class="eyebrow">Administration</p>
        <h1>Indhold</h1>

        <p>
            Teksterne på de offentlige sider.
            En tom linje giver et nyt afsnit.
        </p>

        <!-- Besked når en tekst er gemt -->
        <?php if ($saved !== null): ?>
            <p class="alert" role="status">
                Teksten er gemt.
            </p>
        <?php endif; ?>

        <!-- Viser fejl fra controlleren -->
        <?php if ($error !== null): ?>
            <p class="alert" role="alert">
                <?= e($error) ?>
            </p>
        <?php endif; ?>

        <div class="sidebar-layout">

            <!-- Operatorens navigationsmenu -->
            <?php require __DIR__ . '/../partials/admin-nav.php'; ?>

            <div class="flow">

                <!-- Gennemgår de fire indholdsområder -->
                <?php foreach ($sections as $key => $section): ?>

                    <section
                        class="card"
                        aria-labelledby="<?= e($key) ?>-heading"
                    >

                        <h2
                            class="section-title"
                            id="<?= e($key) ?>-heading"
                        >
                            <?= e($section['label']) ?>
                        </h2>

                        <!-- Kontrollerer at området findes i databasen -->
                        <?php if (!isset($content[$key])): ?>

                            <p role="alert">
                                Indholdet mangler i databasen.
                                Kontrollér seed-data.
                            </p>

                        <?php else: ?>

                            <!-- Formular til dette indholdsområde -->
                            <form
                                class="form"
                                action="<?= e(url('/admin/content')) ?>"
                                method="post"
                            >

                                <!-- Beskytter formularen mod CSRF -->
                                <?= \PostCard\Core\Csrf::field() ?>

                                <!-- Fortæller controlleren hvilket område -->
                                <input
                                    type="hidden"
                                    name="content_key"
                                    value="<?= e($key) ?>"
                                >

                                <!-- Overskrift -->
                                <div class="field">

                                    <label
                                        class="field__label"
                                        for="<?= e($key) ?>-title"
                                    >
                                        Overskrift
                                    </label>

                                    <input
                                        class="field__input"
                                        id="<?= e($key) ?>-title"
                                        name="title"
                                        type="text"
                                        maxlength="150"
                                        required
                                        value="<?= e($content[$key]['title']) ?>"
                                    >

                                </div>

                                <!-- Brødtekst -->
                                <div class="field">

                                    <label
                                        class="field__label"
                                        for="<?= e($key) ?>-body"
                                    >
                                        Tekst
                                    </label>

                                    <textarea
                                        class="field__input"
                                        id="<?= e($key) ?>-body"
                                        name="body"
                                        rows="<?= match ($key) {
                                            'welcome' => 3,
                                            'rules' => 10,
                                            'privacy' => 3,
                                            'contact' => 6,
                                            default => 5
                                        } ?>"
                                        maxlength="10000"
                                        required
                                    ><?= e($content[$key]['body']) ?></textarea>

                                </div>

                                <!-- Sidste ændring og link til offentlig side -->
                                <p class="meta">

                                    Sidst ændret:

                                    <?php if (!empty($content[$key]['updated_at'])): ?>
                                        <time datetime="<?= e($content[$key]['updated_at']) ?>">
                                            <?= e(date(
                                                'd.m.Y H:i',
                                                strtotime($content[$key]['updated_at'])
                                            )) ?>
                                        </time>
                                    <?php else: ?>
                                        Ukendt
                                    <?php endif; ?>

                                    ·

                                    <a href="<?= e(url($section['link'])) ?>">
                                        Se siden
                                    </a>

                                </p>

                                <!-- Gemmer kun denne sektions tekst -->
                                <div class="button-row">

                                    <button
                                        class="button button--primary"
                                        type="submit"
                                    >
                                        <?= e($section['button']) ?>
                                    </button>

                                </div>

                            </form>

                        <?php endif; ?>

                    </section>

                <?php endforeach; ?>

            </div>
        </div>
    </div>
</main>
