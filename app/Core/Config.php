<?php

declare(strict_types=1);

namespace PostCard\Core;

// loads the config once and gives any part of the app one value
class Config
{
    // starts as null and gets filled the first time.
    private static ?array $values = null;

    public static function get(string $key): string
    {
        // First call: read all settings from .env
        if (self::$values === null) {
            self::$values = self::load();
        }

        // A missing setting is a setup mistake, so stop with a clear message
        if (!isset(self::$values[$key])) {
            throw new \RuntimeException('Missing setting ' . $key . ' in .env');
        }

        return self::$values[$key];
    }

    // Reads .env line by line into ['KEY' => 'value'], skipping comments and lines without "="
    private static function load(): array
    {
        $lines = file(
            __DIR__ . '/../../.env',
            FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
        );

        $values = [];
        foreach ($lines as $line) {
            if (str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }
            [$key, $value] = explode('=', $line, 2);
            $values[$key] = $value;
        }

        return $values;
    }
}
