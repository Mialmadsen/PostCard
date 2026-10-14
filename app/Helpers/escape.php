<?php

declare(strict_types=1);

// Make text safe to print in HTML
function e(?string $text): string
{
    // make safe: (the text, or empty if there's none)
    return htmlspecialchars($text ?? '');
}
