<?php

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

session_start();

require __DIR__ . '/../database/data.php';
require __DIR__ . '/../functions/booking.functions.php';
require __DIR__ . '/../functions/pricing.functions.php';
require __DIR__ . '/../functions/guest.functions.php';
require __DIR__ . '/../functions/feature.functions.php';
require __DIR__ . '/../../vendor/autoload.php';

$query = $database->query('SELECT * FROM hotel_info');
$hotelInfo = $query->fetchAll(PDO::FETCH_ASSOC);
$hotelInfo = $hotelInfo[0];

$query = $database->query('SELECT * FROM features');
$featuresInfo = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT room_id, arrival_date, departure_date FROM booking_receipt');
$bookings = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT * FROM guests');
$guests = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT * FROM rooms');
$rooms = $query->fetchAll(PDO::FETCH_ASSOC);

$roomId = (int) $_POST['room_id'];

if (isset(
    $_POST['arrival_date'],
    $_POST['departure_date'],
    $_POST['name'],
    $_POST['payment_method'],
    $_POST['room_id'],
)) {
    if (isset($_POST['offer_id'])) {
        $offerId = (int) $_POST['offer_id'];
    } else {
        $offerId = null;
    }

    $errors = handleBooking(
        $database,
        $hotelInfo,
        $featuresInfo,
        $bookings,
        $guests,
        $rooms,
        $apiKey
    );

    if (!empty($errors)) {
        handleErrors($errors, $roomId, $offerId);
    }
}
