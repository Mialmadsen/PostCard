// Shows a post's photos large with PhotoSwipe, a free MIT-licensed library
// stored in assets/lib/photoswipe/ (no external CDN).
// Phones: pinch with two fingers to zoom and swipe between photos, like Instagram.
// Computers: arrows, keyboard (← → Esc) and click to zoom.
// Without JavaScript each link simply opens the image file.

import PhotoSwipeLightbox from '../lib/photoswipe/photoswipe-lightbox.esm.min.js';

const lightbox = new PhotoSwipeLightbox({
    gallery: '.js-lightbox-gallery',     // the list that holds the photos
    children: '.js-lightbox-link',       // each photo link inside it
    pswpModule: () => import('../lib/photoswipe/photoswipe.esm.min.js'),   // loaded only when a photo is opened
    bgOpacity: 0.95,

    // Danish labels, read aloud by screen readers and shown as tooltips
    closeTitle: 'Luk',
    zoomTitle: 'Zoom',
    arrowPrevTitle: 'Forrige billede',
    arrowNextTitle: 'Næste billede',
    errorMsg: 'Billedet kunne ikke vises.',
    indexIndicatorSep: ' af ',
});

// Finder billedernes rigtige dimensioner, før PhotoSwipe åbnes.
lightbox.options.openPromise = async () => {
    const images = document.querySelectorAll(
        '.js-lightbox-gallery .js-lightbox-link'
    );

    await Promise.all(Array.from(images, async (link) => {
        const img = link.querySelector('img');

        // Bruger dimensionerne, hvis billedet allerede er indlæst.
        if (img && img.complete && img.naturalWidth > 0) {
            link.dataset.pswpWidth = img.naturalWidth;
            link.dataset.pswpHeight = img.naturalHeight;
            return;
        }

        // Ellers indlæses billedet for at finde dimensionerne.
        const image = new Image();
        image.src = link.href;

        try {
            await image.decode();

            link.dataset.pswpWidth = image.naturalWidth;
            link.dataset.pswpHeight = image.naturalHeight;
        } catch (error) {
            console.error('Kunne ikke finde billedets dimensioner:', error);
        }
    }));
};

lightbox.init();
