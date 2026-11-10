/*
 * Likes på PostCard
 *
 * 1. Like og unlike uden genindlæsning.
 * 2. Opdaterer antallet af likes.
 * 3. Viser en popup med brugerne, der har liket opslaget.
 */

// Like eller unlike et opslag
document.addEventListener('submit', async function (event) {
    const form = event.target.closest('[data-like-form]');

    if (!form) {
        return;
    }

    event.preventDefault();

    const button = form.querySelector('.like-button');
    const postItem = form.closest('.post-stats__item');
    const count = postItem?.querySelector('[data-like-count]');

    if (!button || !count || button.disabled) {
        return;
    }

    button.disabled = true;

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error('Kunne ikke gemme like.');
        }

        const result = await response.json();

        if (result.success !== true) {
            throw new Error('Uventet svar fra serveren.');
        }

        // Opdaterer hjertets status
        button.setAttribute('aria-pressed', String(result.liked));
        button.setAttribute(
            'aria-label',
            result.liked ? 'Fjern like' : 'Synes godt om'
        );

        // Opdaterer antallet uden genindlæsning
        count.textContent = result.likes;

    } catch (error) {
        console.error('Like-fejl:', error);
        alert('Dit like kunne ikke gemmes. Prøv igen.');
    } finally {
        button.disabled = false;
    }
});


// Opretter popup'en én gang
const likeDialog = document.createElement('dialog');
likeDialog.className = 'likes-dialog';

likeDialog.innerHTML = `
    <div class="likes-dialog__header">
        <h2>Synes godt om</h2>
        <button type="button"
                class="likes-dialog__close"
                aria-label="Luk oversigten">
            &times;
        </button>
    </div>

    <div class="likes-dialog__content"></div>
`;

document.body.appendChild(likeDialog);

const dialogContent = likeDialog.querySelector('.likes-dialog__content');
const closeButton = likeDialog.querySelector('.likes-dialog__close');

// Lukker popup'en med krydset
closeButton.addEventListener('click', function () {
    likeDialog.close();
});

// Lukker popup'en ved klik uden for boksen
likeDialog.addEventListener('click', function (event) {
    if (event.target === likeDialog) {
        likeDialog.close();
    }
});


// Åbner popup'en, når man klikker på antal likes
document.addEventListener('click', async function (event) {
    const trigger = event.target.closest('[data-like-users]');

    if (!trigger) {
        return;
    }

    const url = trigger.dataset.url;

    dialogContent.textContent = 'Henter likes...';
    likeDialog.showModal();

    try {
        const response = await fetch(url, {
            credentials: 'same-origin',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error('Kunne ikke hente likes.');
        }

        const result = await response.json();

        if (result.success !== true || !Array.isArray(result.users)) {
            throw new Error('Uventet svar fra serveren.');
        }

        dialogContent.replaceChildren();

        // Hvis opslaget ikke har nogen likes
        if (result.users.length === 0) {
            dialogContent.textContent = 'Ingen likes endnu.';
            return;
        }

        // Viser hver bruger, der har liket opslaget
        result.users.forEach(function (user) {
            const row = document.createElement('div');
            row.className = 'likes-dialog__user';

            
            // Viser profilbillede, hvis brugeren har et.
            // Ellers vises brugerens initial.
            const avatar = document.createElement(
                user.avatar_url ? 'img' : 'span'
            );

            avatar.className = 'likes-dialog__avatar';

            
            if (user.avatar_url) {
                avatar.src = user.avatar_url;
                avatar.alt = '';
                avatar.loading = 'lazy';

                // Hvis profilbilledet ikke kan indlæses,
                // erstattes det med brugerens initial.
                avatar.addEventListener('error', function () {
                    const fallback = document.createElement('span');
                    fallback.className = 'likes-dialog__avatar';
                    fallback.textContent = (user.first_name || '?')
                        .charAt(0)
                        .toUpperCase();

                    avatar.replaceWith(fallback);
                }, { once: true });

            } else {
                // Hvis brugeren ikke har et profilbillede
                avatar.textContent = (user.first_name || '?')
                    .charAt(0)
                    .toUpperCase();
            }


            const name = document.createElement('span');
            name.className = 'likes-dialog__name';
            name.textContent = [
                user.first_name,
                user.last_name
            ].filter(Boolean).join(' ');

            row.append(avatar, name);
            dialogContent.appendChild(row);
        });

    } catch (error) {
        console.error('Kunne ikke hente like-oversigt:', error);
        dialogContent.textContent =
            'Kunne ikke hente likes. Prøv igen senere.';
    }
});
