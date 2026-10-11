// Profile picture on settings.php: shows the chosen picture in the round avatar
// before it is saved, so the user can see that the choice worked.
// This is only a convenience: the server checks type and size again.

const MAX_SIZE = 5 * 1024 * 1024;                      // 5 MB, the same as the hint text says
const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

const input = document.querySelector('.js-avatar-input');
const status = document.querySelector('.js-avatar-status');
let preview = document.querySelector('.js-avatar-preview');

if (input && status && preview) {
    input.addEventListener('change', () => {
        const file = input.files[0];
        if (!file) {
            return;                                    // the user closed the dialog without choosing
        }

        if (!ALLOWED_TYPES.includes(file.type) || file.size > MAX_SIZE) {
            input.value = '';                          // do not send a file the server will refuse
            status.textContent = `${file.name} kan ikke bruges. Vælg et JPG-, PNG- eller WebP-billede på højst 5 MB.`;
            return;
        }

        // Swap the initials (or the old picture) for the chosen one, with the same classes
        const image = document.createElement('img');
        image.className = preview.className;
        image.src = URL.createObjectURL(file);
        image.alt = '';                                // decorative: the status text says what was chosen
        preview.replaceWith(image);
        preview = image;

        status.textContent = `Du har valgt ${file.name}. Tryk Gem billede for at gemme det.`;
    });
}
