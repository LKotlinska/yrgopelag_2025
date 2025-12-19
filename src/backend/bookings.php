<?php

declare(strict_types=1);

require __DIR__ . '/../database/data.php';
require __DIR__ . '/functions.php';
require __DIR__ . '/../../vendor/autoload.php';
// require __DIR__ . '/../../view/form.php';

$query = $database->query('SELECT * FROM features');
$featuresInfo = $query->fetchAll(PDO::FETCH_ASSOC);

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
    echo 'Room price: ' . $roomPrice . '   ';
    $featurePrice = (int) calcFeaturePrice($selectedFeatureIds, $featuresInfo);
    $totalCost = $roomPrice + $featurePrice;
    echo 'Total cost:  ' . $totalCost . '   ';
    echo $totalCost;
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
