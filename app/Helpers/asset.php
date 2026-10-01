<?php

/**
 * Returns the URL of a file in public/ with a version number added,
 * e.g. "assets/css/main.css?v=1727777000".
 *
 * The number is the time the file was last changed. When you edit the file,
 * the number changes, so the browser downloads the new version instead of
 * using an old copy from its cache.
 */
function asset(string $path): string
{
    $file = __DIR__ . '/../../public/' . $path;
    $version = is_file($file) ? filemtime($file) : 0;

    return htmlspecialchars($path . '?v=' . $version);
}
