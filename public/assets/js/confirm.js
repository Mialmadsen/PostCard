// Asks "are you sure?" before a form does something that cannot be undone, e.g. deleting a post.
// The question is in the form's data-confirm attribute, the yes-button text in data-confirm-button
// (optional, otherwise "Ja"). It is shown in our own pop-up with the asking seagull.
// Without JavaScript the form is simply sent. This is only a convenience:
// the server must still check that the user may do it.

// The seagull's address, worked out from where this script lives (assets/js/ → assets/images/)
const QUESTION_IMAGE = new URL('../images/illustrations/question-postman.svg', import.meta.url);

document.querySelectorAll('.js-confirm').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (form.dataset.confirmed === 'yes') {
            return;                                    // the user already said yes: send it
        }

        event.preventDefault();                        // stop here and ask first

        // A very old browser without <dialog>: its own simple question box instead
        if (typeof HTMLDialogElement !== 'function') {
            if (window.confirm(form.dataset.confirm)) {
                form.submit();
            }
            return;
        }

        askToConfirm(form);
    });
});

function askToConfirm(form) {
    const dialog = document.createElement('dialog');
    dialog.className = 'dialog dialog--question';
    dialog.setAttribute('aria-labelledby', 'confirm-dialog-text');

    const image = document.createElement('img');
    image.className = 'dialog__illustration';
    image.src = QUESTION_IMAGE;
    image.alt = '';                                    // decorative: the question says it all

    const question = document.createElement('p');
    question.className = 'dialog__text';
    question.id = 'confirm-dialog-text';
    question.textContent = form.dataset.confirm;       // textContent: shown as text, never run as HTML

    const cancel = makeButton('Fortryd', 'button button--secondary');
    const yes = makeButton(form.dataset.confirmButton || 'Ja', 'button button--primary');

    const actions = document.createElement('div');
    actions.className = 'button-row dialog__actions';
    actions.append(cancel, yes);
    dialog.append(image, question, actions);
    document.body.append(dialog);

    cancel.addEventListener('click', () => dialog.close());
    yes.addEventListener('click', () => {
        form.dataset.confirmed = 'yes';
        dialog.close();
        form.requestSubmit();                          // sends the form like a normal click, the check above lets it pass
    });

    // Closed in any way (Fortryd, Ja or Escape): the pop-up is removed again
    dialog.addEventListener('close', () => dialog.remove());

    dialog.showModal();
    cancel.focus();                                    // "Fortryd" first, so Enter by mistake changes nothing
}

function makeButton(text, className) {
    const button = document.createElement('button');
    button.type = 'button';
    button.className = className;
    button.textContent = text;
    return button;
}
