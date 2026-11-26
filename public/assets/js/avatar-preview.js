// Profile picture on settings.php: shows the chosen picture in the round avatar
// before it is saved, so the user can see that the choice worked.
// This is only a convenience: the server checks type and size again.

import { resizeImage } from './image-resize.js';

const MAX_DIMENSION = 1024;

const input = document.querySelector('.js-avatar-input');
const status = document.querySelector('.js-avatar-status');
let preview = document.querySelector('.js-avatar-preview');

// Holder styr på, om billedet er ved at blive behandlet.
let isProcessing = false;

// Holder styr på den midlertidige URL til billedet.
let previewUrl = null;

if (input && status && preview) {
    // Henter den maksimale billedstørrelse fra Admin Settings via HTML.
    const maxSizeMB = Number(input.dataset.maxSizeMb) || 5;
    const MAX_SIZE = maxSizeMB * 1024 * 1024;

    input.addEventListener('change', async () => {
        if (isProcessing) {
            return;
        }

        const file = input.files[0];

        if (!file) {
            return;
        }

        isProcessing = true;
        input.disabled = true;

        status.textContent = `Behandler ${file.name}...`;

        try {
            // Skalerer og komprimerer profilbilledet automatisk.
            const resizedFile = await resizeImage(
                file,
                MAX_DIMENSION,
                MAX_SIZE
            );

            // Erstatter den originale fil med den optimerede fil.
            const transfer = new DataTransfer();
            transfer.items.add(resizedFile);
            input.files = transfer.files;

            // Frigiver hukommelsen fra det tidligere preview.
            if (previewUrl) {
                URL.revokeObjectURL(previewUrl);
            }

            // Opretter en midlertidig URL til det optimerede billede.
            previewUrl = URL.createObjectURL(resizedFile);

            // Swap the initials (or the old picture) for the chosen one,
            // with the same classes.
            const image = document.createElement('img');
            image.className = preview.className;
            image.src = previewUrl;
            image.alt = '';

            preview.replaceWith(image);
            preview = image;

            status.textContent = 
                `Du har valgt ${file.name}. Tryk Gem billede for at gemme det.`;

        } catch (error) {
            // Forhindrer upload af et billede, der ikke kunne behandles.
            input.value = '';
            status.textContent = `${file.name}: ${error.message}`;

        } finally {
            isProcessing = false;
            input.disabled = false;
        }
    });

    // Forhindrer upload, mens billedet stadig behandles.
    input.form.addEventListener('submit', (event) => {
        if (isProcessing) {
            event.preventDefault();

            status.textContent =
                'Vent venligst, mens billedet behandles.';
        }
    });
}