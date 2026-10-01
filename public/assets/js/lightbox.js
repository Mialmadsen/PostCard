// Opens a post's photos large in a dialog, with previous/next and a counter.
// Without JavaScript each link simply opens the image file, so it still works.

const lightboxLinks = [...document.querySelectorAll('.js-lightbox-link')];
const lightbox = document.querySelector('.js-lightbox');

if (lightbox && lightboxLinks.length > 0) {
    const image = lightbox.querySelector('.js-lightbox-image');
    const counter = lightbox.querySelector('.js-lightbox-counter');
    let current = 0;

    // Show photo number "index"; going past the last photo starts again at the first
    const showPhoto = (index) => {
        current = (index + lightboxLinks.length) % lightboxLinks.length;
        const thumbnail = lightboxLinks[current].querySelector('img');
        image.src = lightboxLinks[current].href;
        image.alt = thumbnail.alt;
        counter.textContent = `Billede ${current + 1} af ${lightboxLinks.length}`;
    };

    // Only one photo: no previous/next buttons
    if (lightboxLinks.length === 1) {
        lightbox.querySelector('.js-lightbox-prev').hidden = true;
        lightbox.querySelector('.js-lightbox-next').hidden = true;
    }

    lightboxLinks.forEach((link, index) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();          // stay on the page instead of opening the file
            showPhoto(index);
            lightbox.showModal();            // the browser handles focus, Escape and the backdrop
        });
    });

    lightbox.querySelector('.js-lightbox-close').addEventListener('click', () => lightbox.close());
    lightbox.querySelector('.js-lightbox-prev').addEventListener('click', () => showPhoto(current - 1));
    lightbox.querySelector('.js-lightbox-next').addEventListener('click', () => showPhoto(current + 1));

    // Arrow keys switch photos
    lightbox.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowLeft') showPhoto(current - 1);
        if (event.key === 'ArrowRight') showPhoto(current + 1);
    });

    // A click on the dark backdrop (outside the photo) closes the dialog
    lightbox.addEventListener('click', (event) => {
        if (event.target === lightbox) lightbox.close();
    });
}
