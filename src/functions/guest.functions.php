<?php

declare(strict_types=1);

function isExistingGuest(
    string $name,
    array $guests
): bool {
    foreach ($guests as $guest) {
        if ($name === $guest['name']) {
            return true;
        }
    }
    return false;
};

function getGuestId(
    string $name,
    array $guests
): int {
    foreach ($guests as $guest) {
        if ($name === $guest['name']) {
            $guestId = $guest['id'];
            return $guestId;
        }
    }
    return -1;
};
