<?php
require __DIR__ . '/../../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$apiKey = $_ENV['API_KEY'];

$database = new PDO('sqlite:' . __DIR__ . '/hotel.db');

// $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// $database->exec('PRAGMA foreign_keys = ON');

// ---- UPDATE DATABASE

$bookingQuery = $database->prepare(
    'INSERT INTO room_bookings 
    (arrival_date, departure_date, room_id, guest_id, total_amount, amount_paid, feature_booking_id, transfer_code)
    VALUES (:arrival_date, :departure_date, :room_id, :guest_id, :total_amount, :amount_paid, :feature_booking_id, :transfer_code)'
);

$addGuestQuery = $database->prepare('INSERT INTO guests (name) VALUES (:name)');

$addFeaturesQuery =  $database->prepare('INSERT OR IGNORE INTO features (name, category, tier) VALUES (:name, :category, :tier)');

// ---- DATABASE QUERIES AND FETCHES
$query = $database->query('SELECT * FROM rooms');
$rooms = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT * FROM guests');
$guests = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT room_id, arrival_date, departure_date FROM room_bookings');
$bookings = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT * FROM hotel_info');
$hotelInfo = $query->fetchAll(PDO::FETCH_ASSOC);
$hotelInfo = $hotelInfo[0];

$query = $database->query('SELECT * FROM features');
$featuresInfo = $query->fetchAll(PDO::FETCH_ASSOC);

// FEATURE NAME SHOULD BE AN ID 
// ---- API FETCH
