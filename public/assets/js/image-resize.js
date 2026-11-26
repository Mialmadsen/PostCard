// Fælles funktion til automatisk skalering og komprimering af billeder.
// Genbruges til både opslag og profilbilleder.

export async function resizeImage(file, maxDimension, maxBytes) {
    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

    // Kontrollerer, at billedformatet understøttes.
    if (!allowedTypes.includes(file.type)) {
        throw new Error('Kun JPG, PNG og WebP-billeder er tilladt.');
    }

    // Kontrollerer de angivne størrelsesgrænser.
    if (maxDimension < 1 || maxBytes < 1) {
        throw new Error('Ugyldige billedindstillinger.');
    }

    // En ekstra grænse beskytter browseren mod ekstremt store filer.
    const maxOriginalBytes = 50 * 1024 * 1024;

    if (file.size > maxOriginalBytes) {
        throw new Error('Det originale billede må højst fylde 50 MB.');
    }

    let image;

    try {
        // Indlæser billedet med dets korrekte orientering.
        image = await createImageBitmap(file, {
            imageOrientation: 'from-image'
        });

        const width = image.width;
        const height = image.height;

        // Beskytter mod billeder med ekstrem opløsning.
        if (width * height > 100000000) {
            throw new Error('Billedets opløsning er for høj.');
        }

        // Bevarer billedets proportioner og undgår opskalering.
        const scale = Math.min(
            1,
            maxDimension / Math.max(width, height)
        );

        const newWidth = Math.max(1, Math.round(width * scale));
        const newHeight = Math.max(1, Math.round(height * scale));

        // Beholder originalen, hvis skalering ikke er nødvendig.
        if (scale === 1 && file.size <= maxBytes) {
            return file;
        }

        // Opretter et canvas til billedbehandlingen.
        const canvas = document.createElement('canvas');

        canvas.width = newWidth;
        canvas.height = newHeight;

        const context = canvas.getContext('2d');

        if (!context) {
            throw new Error('Billedet kunne ikke behandles.');
        }

        context.imageSmoothingEnabled = true;
        context.imageSmoothingQuality = 'high';

        // Tegner billedet i den nye størrelse.
        context.drawImage(
            image,
            0,
            0,
            canvas.width,
            canvas.height
        );

        // WebP giver god komprimering og understøtter transparens.
        const outputType = 'image/webp';
        let quality = 0.9;

        while (true) {
            const blob = await new Promise((resolve) => {
                canvas.toBlob(resolve, outputType, quality);
            });

            if (!blob) {
                throw new Error('Billedet kunne ikke komprimeres.');
            }

            // Returnerer billedet, når filstørrelsen er acceptabel.
            if (blob.size <= maxBytes) {
                const extension = blob.type === 'image/webp'
                    ? '.webp'
                    : '.png';

                const name = file.name.replace(/\.[^.]+$/, '');

                return new File(
                    [blob],
                    name + extension,
                    { type: blob.type }
                );
            }

            // Forsøger først at reducere billedkvaliteten.
            if (quality > 0.6) {
                quality = Math.max(0.6, quality - 0.1);
                continue;
            }

            // Reducerer dimensionerne yderligere, hvis nødvendigt.
            if (Math.max(canvas.width, canvas.height) <= 320) {
                throw new Error('Billedet kunne ikke gøres lille nok.');
            }

            canvas.width = Math.max(
                1,
                Math.round(canvas.width * 0.8)
            );

            canvas.height = Math.max(
                1,
                Math.round(canvas.height * 0.8)
            );

            // Tegner igen fra originalen for at bevare kvaliteten.
            context.imageSmoothingEnabled = true;
            context.imageSmoothingQuality = 'high';

            context.drawImage(
                image,
                0,
                0,
                canvas.width,
                canvas.height
            );

            quality = 0.9;
        }
    } finally {
        // Frigiver hukommelsen, også hvis billedbehandlingen fejler.
        if (image) {
            image.close();
        }
    }
}