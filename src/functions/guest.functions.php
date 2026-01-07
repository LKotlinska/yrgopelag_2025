<?php

declare(strict_types=1);

function isExistingGuest(
    PDO $database,
    string $name
): bool {
    $query = $database->prepare(
        'SELECT 1 FROM guests WHERE name = :name LIMIT 1'
    );
    $query->execute([':name' => $name]);

    return (bool) $query->fetchColumn();
}

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

function getOrAddGuest(
    PDO $database,
    string $name,
    array $guests
): int {
    // Check if guest already exists
    if (isExistingGuest($database, $name)) {
        return $guestId = getGuestId($name, $guests);
    } else {
        $addGuestQuery = $database->prepare('INSERT INTO guests (name) VALUES (:name)');
        $addGuestQuery->execute([
            ':name' => $name
        ]);
        $query = $database->query('SELECT id FROM guests ORDER BY id DESC LIMIT 1');
        $guestId = $query->fetch(PDO::FETCH_ASSOC);
        return $guestId = $guestId['id'];
    }
}
