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

$query = $database->query('SELECT room_id, arrival_date, departure_date FROM booking_receipts');
$bookings = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT * FROM guests');
$guests = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT * FROM rooms');
$rooms = $query->fetchAll(PDO::FETCH_ASSOC);


if (isset(
    $_POST['arrival_date'],
    $_POST['departure_date'],
    $_POST['name'],
    $_POST['payment_method'],
    $_POST['room_id'],
)) {
    // Run Api requests
    $result = handleBooking(
        $database,
        $hotelInfo,
        $featuresInfo,
        $bookings,
        $guests,
        $rooms,
        $apiKey
    );

    $roomId = $result['room_id'] ?? (int) $_POST['room_id'];

    // Store errors in session in case booking fails -> used in view/booking.php
    if (isset($result['success']) && $result['success'] === true) {
        $_SESSION['booking_id'] = $result['booking_id'];
        header('Location: ../../view/receipt.php');
        exit;
    }

    // When offer is booked, url must include offer_id to proparly redirect
    $_SESSION['errors'] = $result['errors'];

    // Save all input data to load in case of errors
    $_SESSION['old'] = $_POST;

    $roomId = $result['room_id'];
    $offerId = $result['offer_id'] ?? null;

    $query = "room_id=$roomId";

    if ($offerId !== null) {
        $query .= "&offer_id=$offerId";
    }

    header("Location: ../../view/booking.php?$query#error_msgs");
    exit;
}
