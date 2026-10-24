<?php

declare(strict_types=1);

function danishDate(string $dateTime, string $pattern = 'd. MMM y'): string
{
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
