// Photo preview on the create post form (create-post.php).
// Shows each chosen photo as a small polaroid with a description field (alt text)
// and a "Fjern" button. The photos are added up: choosing again adds to the list.
// This is only a convenience: the server checks number, type and size again.

const MAX_PHOTOS = 5;
const MAX_SIZE = 10 * 1024 * 1024;                     // 10 MB, the same as the hint text says
const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

const input = document.querySelector('.js-photo-input');
const list = document.querySelector('.js-photo-preview');
const status = document.querySelector('.js-photo-status');
const template = document.querySelector('.js-photo-template');

// The chosen photos, in the order they will be shown: { file, url, alt, open }
// (open = whether the description box is unfolded)
let photos = [];

if (input && list && status && template) {
    input.addEventListener('change', () => {
        const messages = [];

        for (const file of input.files) {
            if (!ALLOWED_TYPES.includes(file.type)) {
                messages.push(`${file.name} er ikke et JPG-, PNG- eller WebP-billede.`);
            } else if (file.size > MAX_SIZE) {
                messages.push(`${file.name} er større end 10 MB.`);
            } else if (photos.length >= MAX_PHOTOS) {
                messages.push(`Du kan højst vælge ${MAX_PHOTOS} billeder.`);
                break;
            } else {
                photos.push({ file, url: URL.createObjectURL(file), alt: '', open: false });
            }
        }

        update(messages);
    });
}

// Puts the kept photos back into the file input and draws the previews again.
function update(messages = []) {
    // The browser only sends what is in input.files, so we rebuild it from our list
    const transfer = new DataTransfer();
    photos.forEach((photo) => transfer.items.add(photo.file));
    input.files = transfer.files;

    list.replaceChildren(...photos.map(createItem));

    status.textContent = [`${photos.length} af ${MAX_PHOTOS} billeder valgt.`, ...messages].join(' ');
}

// Makes one preview item from the <template> in the page.
function createItem(photo, index) {
    const number = index + 1;
    const item = template.content.firstElementChild.cloneNode(true);

    const image = item.querySelector('img');
    image.src = photo.url;
    image.alt = `Valgt billede ${number}`;

    // Only the first photo is the cover photo
    item.querySelector('.js-photo-cover').hidden = index !== 0;

    // Keep the description box open or closed when the list is drawn again
    const details = item.querySelector('details');
    details.open = photo.open;
    details.addEventListener('toggle', () => {
        photo.open = details.open;
    });

    // Each description field needs its own id, so its label points to it
    const label = item.querySelector('label');
    const altInput = item.querySelector('input');
    altInput.id = `alt-text-${number}`;
    label.htmlFor = altInput.id;
    altInput.value = photo.alt;
    altInput.addEventListener('input', () => {
        photo.alt = altInput.value;                    // keep the text when the list is drawn again
    });

    const removeButton = item.querySelector('button');
    removeButton.querySelector('.visually-hidden').textContent = `billede ${number}`;
    removeButton.addEventListener('click', () => {
        URL.revokeObjectURL(photo.url);                // free the memory used by the preview
        photos = photos.filter((other) => other !== photo);
        update();
        input.focus();                                 // the button is gone, so move focus somewhere sensible
    });

    return item;
}
