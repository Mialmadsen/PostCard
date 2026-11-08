// Password fields: a "Vis" / "Skjul" button shows or hides what was typed.
// Without JavaScript the button stays hidden and the field works as a normal password field.

const toggles = document.querySelectorAll('.js-password-toggle');

toggles.forEach((toggle) => {
    const input = document.getElementById(toggle.getAttribute('aria-controls'));  // the field this button belongs to
    const text = toggle.querySelector('.js-password-toggle-text');
    if (!input || !text) {
        return;
    }

    toggle.hidden = false;                             // JavaScript works, so the button can be used

    toggle.addEventListener('click', () => {
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        text.textContent = isHidden ? 'Skjul' : 'Vis';
    });

    // Hide the password again before sending, so the browser never saves it as normal text
    input.form?.addEventListener('submit', () => {
        input.type = 'password';
        text.textContent = 'Vis';
    });
});
