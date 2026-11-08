// The message after a redirect (views/partials/flash.php) as a real pop-up.
// Without JavaScript the <dialog open> is already shown in the page, and its OK button closes it.

const dialog = document.querySelector('.js-flash-dialog');

if (dialog && typeof dialog.showModal === 'function') {
    dialog.close();                                    // from "shown in the page" …
    dialog.showModal();                                // … to a pop-up: the page behind is dimmed and inert,
                                                       //   and Escape closes it
    dialog.querySelector('.js-flash-dialog-ok').focus();   // OK, not the optional link, is ready for Enter
}
