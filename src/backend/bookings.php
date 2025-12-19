<?php

declare(strict_types=1);

require __DIR__ . '/../database/data.php';
require __DIR__ . '/../functions/booking.functions.php';
require __DIR__ . '/../functions/receipt.functions.php';
require __DIR__ . '/../functions/pricing.functions.php';
require __DIR__ . '/../functions/guest.functions.php';
require __DIR__ . '/../functions/feature.functions.php';
require __DIR__ . '/../../vendor/autoload.php';

$addGuestQuery = $database->prepare('INSERT INTO guests (name) VALUES (:name)');

$query = $database->query('SELECT * FROM hotel_info');
$hotelInfo = $query->fetchAll(PDO::FETCH_ASSOC);
$hotelInfo = $hotelInfo[0];

$query = $database->query('SELECT * FROM features');
$featuresInfo = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT room_id, arrival_date, departure_date FROM room_bookings');
$bookings = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT * FROM guests');
$guests = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT * FROM rooms');
$rooms = $query->fetchAll(PDO::FETCH_ASSOC);

$bookingQuery = $database->prepare(
    'INSERT INTO room_bookings 
    (arrival_date, departure_date, room_id, guest_id, total_amount, amount_paid, feature_booking_id, transfer_code)
    VALUES (:arrival_date, :departure_date, :room_id, :guest_id, :total_amount, :amount_paid, :feature_booking_id, :transfer_code)'
);

if (isset(
    $_POST['name'],
    $_POST['room_id'],
    $_POST['arrival_date'],
    $_POST['departure_date'],
    $_POST['transfer_code'],
)) {
    $name = (string) trim(filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $roomId = (int) $_POST['room_id'];
    $arrDate = (string) $_POST['arrival_date'];
    $depDate = (string) $_POST['departure_date'];
    $transferCode = (string) trim(filter_var($_POST['transfer_code'], FILTER_SANITIZE_FULL_SPECIAL_CHARS));

    $selectedFeatureIds = $_POST['feature_ids'] ?? [];
    $selectedFeatureIds = array_map('intval', $selectedFeatureIds);
    $selectedFeatures = getFeaturesById($selectedFeatureIds, $featuresInfo);

    $roomPrice = (int) calcRoomPrice($rooms, $roomId, $arrDate, $depDate);
    $featurePrice = (int) calcFeaturePrice($selectedFeatureIds, $featuresInfo);
    $totalCost = calcTotalCost($featurePrice, $roomPrice);

    // Check if guest already exists
    if (isExistingGuest($name, $guests)) {
        $guestId = getGuestId($name, $guests);
    } else {
        $addGuestQuery->execute([
            ':name' => $name
        ]);
        // --------------------------- THIS ISNT WORKING PROPARLY - FIX LATERRRRR
        $guestId = $database->lastInsertId();
    }

    $bookedRooms = getBookedRooms($bookings, $arrDate);

    if (!isRoomAvailable($roomId, $bookedRooms)) {
        echo 'Sorry, chosen room is unavailable';
    } else {
        echo 'VALIDATION RESPONSE';
        $validationResponse = validateTransferCode($transferCode, $totalCost);
        print_r($validationResponse);
        if (
            isset($validationResponse['status'])
            && $validationResponse['status'] === 'success'
        ) {
            $receiptResponse = sendReceipt($hotelInfo, $apiKey, $name, $arrDate, $depDate, $selectedFeatures);
            echo 'Send Receipt response:';
            print_r($receiptResponse);
            if (
                isset($receiptResponse['status'])
                && $receiptResponse['status'] === 'success'
            ) {
                $bookingQuery->execute([
                    ':arrival_date' => $arrDate,
                    ':departure_date' => $depDate,
                    ':room_id' => $roomId,
                    ':guest_id' => $guestId,
                    ':total_amount' => $totalCost,
                    ':amount_paid' => $totalCost,
                    ':transfer_code' => $transferCode,
                ]);
                echo 'DEPOSIT RESPONSE:';
                $depositResponse = consumeTransferCode($hotelInfo, $transferCode);
                print_r($depositResponse);
                echo 'Booking successfull';
            } else {
                echo $response['error'] ?? 'Receipt delievery failed';
            }
        } else {
            echo $response['error'] ?? 'Transfer validation failed';
        }
    }
}

// ADD ANOTHER PAGE FOR VIEW OF SUCCESSFULL BOOKING

// header('Location: /../index.php');
