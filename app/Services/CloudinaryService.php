<?php

declare(strict_types=1);

namespace PostCard\Services;

use PostCard\Core\Config;

// Fælles service til billedupload til Cloudinary.
// Kan genbruges til både opslag og profilbilleder.
class CloudinaryService
{
    private string $cloudName;
    private string $apiKey;
    private string $apiSecret;

    public function __construct()
    {
        // Henter Cloudinary-oplysninger fra projektets fælles Config-klasse.
        $this->cloudName = Config::get('CLOUDINARY_CLOUD_NAME');
        $this->apiKey = Config::get('CLOUDINARY_API_KEY');
        $this->apiSecret = Config::get('CLOUDINARY_API_SECRET');
    }

    // Kontrollerer billedfilen, inden den sendes til Cloudinary.
    // Funktionen kan bruges til både opslag og profilbilleder.
    public function validateImage(array $file): void
    {
        // Kontrollerer at filen er modtaget korrekt.
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Billedet kunne ikke uploades.');
        }

        // Maksimal filstørrelse: 5 MB.
        $maxSize = 5 * 1024 * 1024;

        if (($file['size'] ?? 0) <= 0 || $file['size'] > $maxSize) {
            throw new \RuntimeException('Billedet må højst fylde 5 MB.');
        }

        // Kontrollerer at filen er en rigtig PHP-upload.
        if (
            !isset($file['tmp_name']) ||
            !is_uploaded_file($file['tmp_name'])
        ) {
            throw new \RuntimeException('Ugyldig billedfil.');
        }

        // Finder filtypen ud fra filens indhold,
        // ikke blot filnavnet eller filendelsen.
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        // Tilladte billedtyper.
        $allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];

        if (!in_array($mimeType, $allowedTypes, true)) {
            throw new \RuntimeException(
                'Kun JPG, PNG og WebP-billeder er tilladt.'
            );
        }
    }

    // Uploader et valideret billede til Cloudinary.
    // Returnerer billedets public_id og andre oplysninger.
    public function upload(array $file): array
    {
        // Genbruger vores eksisterende sikkerhedsvalidering.
        $this->validateImage($file);

        // Cloudinary kræver et timestamp til signed uploads.
        $timestamp = time();

        // 'authenticated' betyder, at billedet ikke leveres
        // via en almindelig offentlig Cloudinary-URL.
        $params = [
            'timestamp' => $timestamp,
            'type' => 'authenticated'
        ];

        // Cloudinary-signaturen beregnes ud fra
        // parametrene sorteret alfabetisk.
        ksort($params);
        $signatureParts = [];

        foreach ($params as $key => $value) {
            $signatureParts[] = $key . '=' . $value;
        }

        $signature = sha1(
            implode('&', $signatureParts) . $this->apiSecret
        );

        // Bygger uploadformularen til Cloudinary.
        $postFields = [
            'file' => new \CURLFile($file['tmp_name']),
            'api_key' => $this->apiKey,
            'timestamp' => $timestamp,
            'type' => 'authenticated',
            'signature' => $signature
        ];

        $endpoint = 'https://api.cloudinary.com/v1_1/'
            . $this->cloudName . '/image/upload';

        // Opretter en sikker HTTPS-forbindelse.
        $curl = curl_init($endpoint);

        if ($curl === false) {
            throw new \RuntimeException(
                'Kunne ikke starte billedupload.'
            );
        }

        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        // Stopper, hvis Cloudinary afviser uploaden.
        if ($response === false || $httpCode < 200 || $httpCode >= 300) {
            throw new \RuntimeException(
                'Billedet kunne ikke uploades til Cloudinary.'
            );
        }

        $data = json_decode($response, true);

        // Kontrollerer at Cloudinary returnerede
        // de oplysninger, vi skal bruge.
        if (
            !is_array($data) ||
            !isset($data['public_id'], $data['version']) ||
            ($data['type'] ?? null) !== 'authenticated'
        ) {
            throw new \RuntimeException(
                'Ugyldigt svar fra Cloudinary.'
            );
        }

        return [
            'public_id' => $data['public_id'],
            'version' => $data['version'],
            'format' => $data['format'] ?? null
        ];
    }

    // Sletter et billede fra Cloudinary ved hjælp af dets public_id.
    // Bruges til oprydning, hvis oprettelse af et opslag mislykkes.
    public function delete(string $publicId): void
    {
        // Cloudinary kræver et timestamp til signed requests.
        $timestamp = time();

        // Parametrene, der skal signeres.
        $params = [
            'public_id' => $publicId,
            'timestamp' => $timestamp,
            'type' => 'authenticated'
        ];

        ksort($params);
        $signatureParts = [];

        foreach ($params as $key => $value) {
            $signatureParts[] = $key . '=' . $value;
        }

        $signature = sha1(
            implode('&', $signatureParts) . $this->apiSecret
        );

        // Sender en underskrevet sletteanmodning til Cloudinary.
        $postFields = [
            'public_id' => $publicId,
            'timestamp' => $timestamp,
            'type' => 'authenticated',
            'api_key' => $this->apiKey,
            'signature' => $signature
        ];

        $endpoint = 'https://api.cloudinary.com/v1_1/'
            . $this->cloudName . '/image/destroy';

        $curl = curl_init($endpoint);

        if ($curl === false) {
            throw new \RuntimeException('Kunne ikke starte sletning af billede.');
        }

        curl_setopt_array($curl, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($response === false || $httpCode < 200 || $httpCode >= 300) {
            throw new \RuntimeException('Billedet kunne ikke slettes fra Cloudinary.');
        }

        $data = json_decode($response, true);

        if (!is_array($data) || ($data['result'] ?? null) !== 'ok') {
            throw new \RuntimeException('Cloudinary bekræftede ikke sletningen.');
        }
    }

    // Henter et privat billede fra Cloudinary.
    // Funktionen bruges først, når controlleren har
    // kontrolleret brugerens adgang til gruppen.
    public function downloadImage(
        string $publicId,
        int $version,
        string $format
    ): string {
        // Tillader kun de billedformater, vi bruger i PostCard.
        if (!in_array($format, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            throw new \RuntimeException('Ugyldigt billedformat.');
        }

        // Kontrollerer, at Cloudinary-oplysningerne er gyldige.
        if (
            $publicId === '' ||
            !preg_match('/^[a-zA-Z0-9_\/-]+$/', $publicId) ||
            $version < 1
        ) {
            throw new \RuntimeException('Ugyldige billedoplysninger.');
        }

        // Bygger stien til det private Cloudinary-billede.
        $imagePath = 'v'
            . $version . '/'
            . $publicId . '.'
            . $format;

        // Opretter en signatur til Cloudinary-billedet.
        $signature = rtrim(
            strtr(
                base64_encode(
                    sha1($imagePath . $this->apiSecret, true)
                ),
                '+/',
                '-_'
            ),
            '='
        );

        $signature = substr($signature, 0, 8);

        // Bygger en signeret Cloudinary-URL.
        $url = 'https://res.cloudinary.com/'
            . $this->cloudName
            . '/image/authenticated/s--'
            . $signature
            . '--/v'
            . $version
            . '/'
            . $publicId
            . '.'
            . $format;

        // Henter billedet direkte fra Cloudinary til PHP.
        $curl = curl_init($url);

        if ($curl === false) {
            throw new \RuntimeException('Kunne ikke hente billedet.');
        }

        curl_setopt_array($curl, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        // Stopper, hvis Cloudinary ikke returnerer billedet.
        if (
            $response === false ||
            $httpCode !== 200
        ) {
            throw new \RuntimeException(
                'Cloudinary returnerede HTTP-status: ' . $httpCode
            );
        }

        return $response;
    }
}
