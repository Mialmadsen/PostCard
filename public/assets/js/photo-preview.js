// Photo list on the post forms (create-post.php and edit-post.php).
// Shows each photo as a small polaroid with a "Forsidebillede" choice, a description field (alt text)
// and a "Fjern" button. New photos are added up: choosing again adds to the list.
// On edit-post.php the list starts with the post's saved photos, which PHP puts in the page.
// This is only a convenience: the server checks number, type and size again.

const MAX_PHOTOS = 5;                                  // saved and new photos together
const MAX_SIZE = 10 * 1024 * 1024;                     // 10 MB, the same as the hint text says
const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

const input = document.querySelector('.js-photo-input');
const list = document.querySelector('.js-photo-preview');
const status = document.querySelector('.js-photo-status');
const template = document.querySelector('.js-photo-template');

// Every photo in the list, in the order they are shown:
// a saved photo: { id, url, alt, open }   (id = post_images.image_id)
// a new photo:   { file, url, alt, open }
// (open = whether the description box is unfolded)
let photos = [];
let cover = null;                                      // the photo chosen as cover photo

if (input && list && status && template) {
    // edit-post.php: read the saved photos PHP put in the list. The first one is the cover today.
    list.querySelectorAll('[data-image-id]').forEach((item) => {
        photos.push({
            id: item.dataset.imageId,
            url: item.querySelector('img').src,
            alt: item.querySelector('.js-photo-alt').value,
            open: false,
        });
    });
    if (photos.length > 0) {
        cover = photos[0];
        update();
    }

    input.addEventListener('change', () => {
        const messages = [];

        for (const file of input.files) {
            if (!ALLOWED_TYPES.includes(file.type)) {
                messages.push(`${file.name} er ikke et JPG-, PNG- eller WebP-billede.`);
            } else if (file.size > MAX_SIZE) {
                messages.push(`${file.name} er større end 10 MB.`);
            } else if (photos.length >= MAX_PHOTOS) {
                messages.push(`Du kan højst have ${MAX_PHOTOS} billeder.`);
                break;
            } else {
                photos.push({ file, url: URL.createObjectURL(file), alt: '', open: false });
            }
        }

        update(messages);
    });
}

// Puts the new photos back into the file input and draws the list again.
function update(messages = []) {
    // The browser only sends what is in input.files, so we rebuild it from our list
    const transfer = new DataTransfer();
    photos.filter((photo) => photo.file).forEach((photo) => transfer.items.add(photo.file));
    input.files = transfer.files;

    // A post needs at least one photo: when no saved photo is left, a new one is required
    input.required = !photos.some((photo) => photo.id);

    // If the cover photo was removed (or none is chosen yet), the first photo becomes the cover
    if (!photos.includes(cover)) {
        cover = photos[0] ?? null;
    }

    list.replaceChildren(...photos.map(createItem));

    status.textContent = [`${photos.length} af ${MAX_PHOTOS} billeder valgt.`, ...messages].join(' ');
}

// Makes one list item from the <template> in the page.
function createItem(photo, index) {
    const number = index + 1;
    const item = template.content.firstElementChild.cloneNode(true);

    const image = item.querySelector('img');
    image.src = photo.url;
    image.alt = `Billede ${number}`;

    // "Forsidebillede": all radio buttons share name="cover", so only one can be chosen.
    // The value tells PHP which photo: "saved-11" (post_images id) or "new-0" (its place among the new files).
    const coverInput = item.querySelector('.js-photo-cover');
    const coverLabel = item.querySelector('.js-photo-cover-label');
    coverInput.id = `cover-${number}`;
    coverLabel.htmlFor = coverInput.id;
    coverInput.value = photo.id
        ? `saved-${photo.id}`
        : `new-${photos.filter((other) => other.file).indexOf(photo)}`;
    coverInput.checked = photo === cover;
    coverInput.addEventListener('change', () => {
        cover = photo;
    });

    // Keep the description box open or closed when the list is drawn again
    const details = item.querySelector('details');
    details.open = photo.open;
    details.addEventListener('toggle', () => {
        photo.open = details.open;
    });

    // Each description field needs its own id, so its label points to it.
    // Saved photos send their id with the text; new photos are matched by order.
    const altLabel = item.querySelector('.js-photo-alt-label');
    const altInput = item.querySelector('.js-photo-alt');
    altInput.id = `alt-text-${number}`;
    altLabel.htmlFor = altInput.id;
    altInput.name = photo.id ? `saved_alt_text[${photo.id}]` : 'alt_text[]';
    altInput.value = photo.alt;
    altInput.addEventListener('input', () => {
        photo.alt = altInput.value;                    // keep the text when the list is drawn again
    });

    const removeButton = item.querySelector('.js-photo-remove');
    removeButton.querySelector('.visually-hidden').textContent = `billede ${number}`;
    removeButton.addEventListener('click', () => {
        if (photo.id) {
            // A saved photo: tell the server to delete it when the form is saved
            const field = document.createElement('input');
            field.type = 'hidden';
            field.name = 'remove_images[]';
            field.value = photo.id;
            input.form.append(field);
        } else {
            URL.revokeObjectURL(photo.url);            // free the memory used by the preview
        }
        photos = photos.filter((other) => other !== photo);
        update();
        input.focus();                                 // the button is gone, so move focus somewhere sensible
    });

    return item;
}
