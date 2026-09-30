<?php
$pageTitle = 'Style guide';
$isLoggedIn = false;
$pageStyle = 'styleguide';
require __DIR__ . '/../views/partials/header.php';
?>

<main id="main" class="site-main">
    <div class="container flow">
        <h1>Style guide</h1>
        <p>Design tokens and components for <span class="brand">PostCard</span>. Developer page, not linked from the site.</p>

        <!-- Colours -->
        <section class="card flow" aria-labelledby="colours-heading">
            <h2 id="colours-heading">Farver</h2>
            <ul class="swatch-list">
                <li class="swatch">
                    <div class="swatch__colour" style="background: var(--color-dusty-blue)"></div>
                    <p class="swatch__label">Dusty blue<br>#A9C4CE · page background</p>
                </li>
                <li class="swatch">
                    <div class="swatch__colour" style="background: var(--color-cream)"></div>
                    <p class="swatch__label">Cream<br>#F5F0E6 · cards</p>
                </li>
                <li class="swatch">
                    <div class="swatch__colour" style="background: var(--color-sand)"></div>
                    <p class="swatch__label">Sand<br>#E8DFD0 · secondary, footer</p>
                </li>
                <li class="swatch">
                    <div class="swatch__colour" style="background: var(--color-paper-white)"></div>
                    <p class="swatch__label">Paper white<br>#FBF8F2 · letters</p>
                </li>
                <li class="swatch">
                    <div class="swatch__colour" style="background: var(--color-ink)"></div>
                    <p class="swatch__label">Ink<br>#1E1E1C · text</p>
                </li>
                <li class="swatch">
                    <div class="swatch__colour" style="background: var(--color-terracotta)"></div>
                    <p class="swatch__label">Terracotta<br>#A34E27 · buttons, links</p>
                </li>
                <li class="swatch">
                    <div class="swatch__colour" style="background: var(--color-deep-blue)"></div>
                    <p class="swatch__label">Deep blue<br>#3F5F6B · secondary, focus</p>
                </li>
                <li class="swatch">
                    <div class="swatch__colour" style="background: var(--color-grey)"></div>
                    <p class="swatch__label">Grey<br>#5C6468 · meta text</p>
                </li>
                <li class="swatch">
                    <div class="swatch__colour" style="background: var(--color-apricot-dark)"></div>
                    <p class="swatch__label">Apricot dark<br>#CF7C4B · decoration only</p>
                </li>
                <li class="swatch">
                    <div class="swatch__colour" style="background: var(--color-apricot)"></div>
                    <p class="swatch__label">Apricot<br>#E39462 · decoration only</p>
                </li>
                <li class="swatch">
                    <div class="swatch__colour" style="background: var(--color-apricot-light)"></div>
                    <p class="swatch__label">Apricot light<br>#EAA577 · decoration only</p>
                </li>
            </ul>
        </section>

        <!-- Typography -->
        <section class="card flow" aria-labelledby="type-heading">
            <h2 id="type-heading">Typografi</h2>

            <div>
                <p class="type-sample__meta">Display · Jost Light · spaced capitals</p>
                <p class="type-sample--display">PostCard</p>
            </div>
            <div>
                <p class="type-sample__meta">Eyebrow · small capitals label above a heading</p>
                <p class="eyebrow">Hilsen fra PostCard</p>
            </div>
            <div>
                <p class="type-sample__meta">H1 · Jost Medium · 30 → 44px</p>
                <p style="font-size: var(--text-h1)">Sommer i Skagen</p>
            </div>
            <div>
                <p class="type-sample__meta">H2 · Jost Medium · 24 → 32px</p>
                <p style="font-size: var(--text-h2)">Familien Nielsen</p>
            </div>
            <div>
                <p class="type-sample__meta">H3 · Jost Medium · 20 → 24px</p>
                <p style="font-size: var(--text-h3)">Nyeste kommentarer</p>
            </div>
            <div>
                <p class="type-sample__meta">Body · Jost Regular · 16px</p>
                <p>Del din oplevelse – ikke hele dit liv. <span class="brand">PostCard</span> er et privat sted at dele rejseminder med de
                    mennesker, der betyder noget.</p>
            </div>
            <div>
                <p class="type-sample__meta">Small · muted · 14px</p>
                <p><small class="type-sample__muted">3 dage siden · Positano, Italien</small></p>
            </div>
            <div>
                <p class="type-sample__meta">Handwriting · Caveat · accents only</p>
                <p class="type-sample--hand">Hilsen fra Skagen ♡</p>
            </div>
            <div>
                <p class="type-sample__meta">Link</p>
                <p><a href="#main">Se alle oplevelser</a></p>
            </div>
        </section>

        <!-- Buttons -->
        <section class="card flow" aria-labelledby="buttons-heading">
            <h2 id="buttons-heading">Knapper</h2>
            <p class="type-sample__meta">One primary button per screen. Links that look like buttons use &lt;a&gt;,
                actions use &lt;button&gt;.</p>
            <div class="button-row">
                <button class="button button--primary" type="button">Opret bruger</button>
                <button class="button button--secondary" type="button">Annuller</button>
            </div>
        </section>

        <!-- Forms -->
        <section class="card flow" aria-labelledby="forms-heading">
            <h2 id="forms-heading">Formularer</h2>
            <p class="alert alert--error" role="alert">Forkert e-mail eller adgangskode.</p>
            <p class="alert alert--success" role="status">Linket er sendt.</p>
            <form class="form" action="#" method="post">
                <div class="field">
                    <label class="field__label" for="demo-name">Normalt felt</label>
                    <input class="field__input" id="demo-name" type="text" aria-describedby="demo-name-hint">
                    <p class="field__hint" id="demo-name-hint">En hjælpetekst under feltet.</p>
                </div>
                <div class="field">
                    <label class="field__label" for="demo-email">Felt med fejl</label>
                    <input class="field__input" id="demo-email" type="email" value="mia@" aria-invalid="true" aria-describedby="demo-email-error">
                    <p class="field__error" id="demo-email-error">Skriv en gyldig e-mail, fx mia@eksempel.dk.</p>
                </div>
                <div class="field field--checkbox">
                    <input class="field__checkbox" id="demo-check" type="checkbox">
                    <label for="demo-check">Et afkrydsningsfelt</label>
                </div>
            </form>
        </section>

        <!-- Spacing -->
        <section class="card flow" aria-labelledby="space-heading">
            <h2 id="space-heading">Afstande</h2>
            <ul class="space-list">
                <li class="space-list__item"><span class="space-list__bar" style="width: var(--space-2xs)"></span> 2xs ·
                    4px</li>
                <li class="space-list__item"><span class="space-list__bar" style="width: var(--space-xs)"></span> xs ·
                    8px</li>
                <li class="space-list__item"><span class="space-list__bar" style="width: var(--space-sm)"></span> sm ·
                    16px</li>
                <li class="space-list__item"><span class="space-list__bar" style="width: var(--space-md)"></span> md ·
                    24px</li>
                <li class="space-list__item"><span class="space-list__bar" style="width: var(--space-lg)"></span> lg ·
                    32px</li>
                <li class="space-list__item"><span class="space-list__bar" style="width: var(--space-xl)"></span> xl ·
                    48px</li>
            </ul>
        </section>



    </div>
</main>

<?php require __DIR__ . '/../views/partials/footer.php'; ?>