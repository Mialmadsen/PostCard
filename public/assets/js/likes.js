
document.addEventListener('submit', async function (event) {
    const form = event.target.closest('[data-like-form]');

    if (!form) {
        return;
    }

    event.preventDefault();

    const button = form.querySelector('.like-button');
    const count = form.querySelector('[data-like-count]');

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

        button.setAttribute('aria-pressed', String(result.liked));
        button.setAttribute(
            'aria-label',
            result.liked ? 'Fjern like' : 'Synes godt om'
        );

        count.textContent = result.likes;

    } catch (error) {
        console.error('Like-fejl:', error);
        alert('Dit like kunne ikke gemmes. Prøv igen.');
    } finally {
        button.disabled = false;
    }
});
