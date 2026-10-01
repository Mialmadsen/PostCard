// Asks "are you sure?" before a form does something that cannot be undone,
// e.g. deleting a post. The question is in the form's data-confirm attribute.
// This is only a convenience: the server must still check that the user may do it.

document.querySelectorAll('.js-confirm').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm(form.dataset.confirm)) {
            event.preventDefault();          // the user chose "Annuller": do not send the form
        }
    });
});
