<?php

declare(strict_types=1);
//It is a file for reading what the user sent 
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

//reads from POST
function inputId(string $key): ?int
{
    $id = filter_var(input($key), FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1],
    ]);
    return $id === false ? null : $id;
}

//reads from GET
function queryId(string $key): ?int
{
    // From the address
    $id = filter_input(INPUT_GET, $key, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1],
    ]);

    // Only a real int counts. null and false both become null
    return (is_int($id)) ? $id : null;
}
