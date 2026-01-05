<?php

require __DIR__ . '/../../src/database/data.php';
$query = $database->query('SELECT * FROM rooms');
$rooms = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT * FROM room_amenity JOIN amenities ON room_amenity.amenity_id = amenities.id');
$amenities = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <?php require __DIR__ . '/rooms.php'; ?>

    <?php require __DIR__ . '/index.offers.php'; ?>
</main>