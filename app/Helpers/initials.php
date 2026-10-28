<?php

declare(strict_types=1);

// Two capital letters for an avatar: "Mia" + "Nielsen" → "MN".
// mb_ functions count letters, not bytes, so Æ, Ø and Å are not cut in half.
function initials(string $firstName, string $lastName): string
{
    $first = mb_substr($firstName, 0, 1);
    $last = mb_substr($lastName, 0, 1);

    return mb_strtoupper($first . $last);
}
