<?php

declare(strict_types=1);

// Read a POST field safely: always a string ('' if missing or not text), trimmed unless $trim is false.
function input(string $key, bool $trim = true): string
{
    // The field, or '' if the form didn't send it.
    $value = $_POST[$key] ?? '';

    // A field like name[]=x arrives as an array: treat it as empty instead of crashing.
    if (!is_string($value)) {
        return '';
    }

    // Remove spaces at both ends, unless the caller says not to (passwords).
    return $trim ? trim($value) : $value;
}
