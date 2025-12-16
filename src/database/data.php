<?php

$database = new PDO('sqlite:' . __DIR__ . '/../database/hotel.db');

$query = $database->query('SELECT * FROM rooms');
$rooms = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT room_id, arrival_date, departure_date 
                           FROM room_bookings');
$bookings = $query->fetchAll(PDO::FETCH_ASSOC);

$bookingQuery = $database->prepare(
    'INSERT INTO room_bookings 
    (arrival_date, departure_date, room_id, guest_id, transfer_code)
    VALUES (:arrival_date, :departure_date, :room_id, :guest_id, :transfer_code)'
);

$guestQuery = $database->prepare('INSERT INTO guests (name) VALUES (:name)');

?>
<script>
    const rooms = <?= json_encode($rooms); ?>;
</script>