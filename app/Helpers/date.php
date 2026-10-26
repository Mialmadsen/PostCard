<?php

declare(strict_types=1);

function danishDate(string $dateTime, string $pattern = 'd. MMM y'): string
{
    // Use IntlDateFormatter if the intl extension is available
    if (class_exists(\IntlDateFormatter::class)) {
        $formatter = new \IntlDateFormatter(
            'da_DK',                     // language + country
            \IntlDateFormatter::NONE,    // no ready-made date style, we use our pattern
            \IntlDateFormatter::NONE,    // no time style either
            null,                        // time zone: null = PHP's, which is now Copenhagen
            null,                        // calendar: null = the normal (Gregorian) one
            $pattern
        );

        $date = new \DateTimeImmutable($dateTime);

        return $formatter->format($date);
    }

    // Fallback if the intl extension is not installed
    $date = new \DateTimeImmutable($dateTime);

    // Danish month abbreviations
    $months = [
        1 => 'jan.', 2 => 'feb.', 3 => 'mar.', 4 => 'apr.',
        5 => 'maj', 6 => 'jun.', 7 => 'jul.', 8 => 'aug.',
        9 => 'sep.', 10 => 'okt.', 11 => 'nov.', 12 => 'dec.'
    ];

    // Date patterns currently used in PostCard
    $replacements = [
        'dd.MM.y HH:mm' => $date->format('d.m.Y H:i'),
        'dd.MM.y' => $date->format('d.m.Y'),
        'd. MMM y' => $date->format('j. ') . $months[(int) $date->format('n')] . ' ' . $date->format('Y'),
        'd. MMM' => $date->format('j. ') . $months[(int) $date->format('n')],
        'MMM' => $months[(int) $date->format('n')],
        'd' => $date->format('j')
    ];

    return $replacements[$pattern] ?? $date->format('d.m.Y');
}
