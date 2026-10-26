<?php

declare(strict_types=1);

// Danish date text, the same on every computer and host 
function danishDate(string $dateTime, string $pattern = 'd. MMM y'): string
{
    $date = new \DateTimeImmutable($dateTime);

    // Danish short month names: "maj" is already short, so it has no period
    $months = [
        1 => 'jan.',
        2 => 'feb.',
        3 => 'mar.',
        4 => 'apr.',
        5 => 'maj',
        6 => 'jun.',
        7 => 'jul.',
        8 => 'aug.',
        9 => 'sep.',
        10 => 'okt.',
        11 => 'nov.',
        12 => 'dec.',
    ];
    $month = $months[(int) $date->format('n')];

    // The patterns PostCard uses; any other pattern is a programming mistake, so stop with a clear message
    return match ($pattern) {
        'd. MMM y' => $date->format('j. ') . $month . ' ' . $date->format('Y'),
        'd. MMM' => $date->format('j. ') . $month,
        'MMM' => $month,
        'd' => $date->format('j'),
        'dd.MM.y' => $date->format('d.m.Y'),
        'dd.MM.y HH:mm' => $date->format('d.m.Y H:i'),
        default => throw new \InvalidArgumentException('danishDate() does not know the pattern: ' . $pattern),
    };
}
