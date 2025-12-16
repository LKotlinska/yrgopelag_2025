<?php

$database = new PDO('sqlite:' . __DIR__ . '/../database/hotel.db');

$query = $database->query('SELECT id, tier FROM rooms');
$rooms = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT room_id, arrival_date, departure_date 
                           FROM room_bookings');
$bookings = $query->fetchAll(PDO::FETCH_ASSOC);
