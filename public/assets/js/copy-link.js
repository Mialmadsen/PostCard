// "Kopiér link" on invite.php: copies the new invitation link to the clipboard.
// Without JavaScript the link is still in a read-only field, so it can be selected and copied by hand.

const input = document.querySelector('.js-copy-input');
const button = document.querySelector('.js-copy-button');
const status = document.querySelector('.js-copy-status');

if (input && button) {
    button.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(input.value);
            status.textContent = 'Linket er kopieret.';
        } catch {
            // The browser said no (e.g. no permission): select the text so the user can copy it
            input.select();
            status.textContent = 'Kopiér linket med Ctrl+C (Cmd+C på Mac).';
        }
    });
}
