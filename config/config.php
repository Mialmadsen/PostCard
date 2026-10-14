<?php
// This file turns text in .env into php so that we can connect to the database
$lines = file(
    __DIR__ . '/../.env',
    FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
);

$config = [];
foreach ($lines as $line) {
    if (str_starts_with($line, '#')) continue; //divider - we divide into to parts: key and value
    [$key, $value] = explode('=', $line, 2);
    $config[$key] = $value; //store the key-value pair in the config array
}
return $config;
