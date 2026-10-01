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

lightbox.init();
