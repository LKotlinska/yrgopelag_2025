<?php

declare(strict_types=1);

require __DIR__ . '/../database/data.php';
require __DIR__ . '/functions.php';
require __DIR__ . '/../../view/form.php';

if (isset(
    $_POST['name'],
    $_POST['room-id'],
    $_POST['arrival-date'],
    $_POST['departure-date'],
    $_POST['transfer-code'],
    $_POST['total-price']
)) {
    $name = (string) trim(filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $roomId = (int) $_POST['room-id'];
    $arrDate = (string) $_POST['arrival-date'];
    $depDate = (string) $_POST['departure-date'];
    $transferCode = (string) trim(filter_var($_POST['transfer-code'], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $roomPrice = (int) calculateRoomPrice($rooms, $roomId, $arrDate, $depDate);

    // Check if guest already exists
    if (isExistingGuest($name, $guests)) {
        $guestId = getGuestId($name, $guests);
    } else {
        $addGuestQuery->execute([
            ':name' => $name
        ]);
        // --------------------------- THIS ISNT WORKING PROPARLY
        $guestId = $database->lastInsertId();
    }

    $bookedRooms = getBookedRooms($bookings, $arrDate);

    if (!isRoomAvailable($roomId, $bookedRooms)) {
        echo 'Sorry, chosen room is unavailable';
    } else {
        $response = validateTransferCode($transferCode, $roomPrice);
        print_r($response);
        if (
            isset($response['status'])
            && $response['status'] === 'success'
        ) {
            $bookingQuery->execute([
                ':arrival_date' => $arrDate,
                ':departure_date' => $depDate,
                ':room_id' => $roomId,
                ':guest_id' => 1,
                ':total_amount' => $roomPrice,
                ':amount_paid' => 0,
                ':feature_booking_id' => null,
                ':transfer_code' => $transferCode,
            ]);
        } else {
            echo $response['error'] ?? 'Transfer validation failed';
        }
        echo 'Booking successfull';
    }
}
// header('Location: /../index.php');
