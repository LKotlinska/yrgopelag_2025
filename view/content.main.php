<?php

require __DIR__ . '/../src/database/data.php';
$query = $database->query('SELECT * FROM rooms');
$rooms = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT * FROM room_amenity JOIN amenities ON room_amenity.amenity_id = amenities.id');
$amenities = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<main>

    <section>
        <article class="intro-card">
            <p>At Terracotta Bay, time moves a little slower.</p>
            <p>Nestled by the shoreline, where warm stone meets soft sand and the sea sets the rhythm, our spa hotel is designed for rest, renewal, and quiet moments of presence.</p>
            <p>Everything is shaped to help you feel at ease.</p>
        </article>
    </section>

    <?php require __DIR__ . '/rooms.php' ?>

</main>