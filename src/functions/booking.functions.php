<?php

declare(strict_types=1);

function getBookedRooms(
    array $bookings,
    string $date
): array {
    $unavailableRooms = [];
    foreach ($bookings as $booking) {
        if (
            $date >= $booking['arrival_date'] &&
            $date <  $booking['departure_date']
        ) {
            $unavailableRooms[] = (int)$booking['room_id'];
        }
    }
    return $unavailableRooms;
};

function isRoomAvailable(
    int $roomId,
    array $bookedRooms
): bool {
    return !in_array($roomId, $bookedRooms, true);
};

function handleBooking(
    PDO $database,
    array $hotelInfo,
    array $featuresInfo,
    array $bookings,
    array $guests,
    array $rooms,
    string $apiKey
) {
    // Declare and sanitize
    $name = (string) trim(filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $roomId = (int) $_POST['room_id'];
    $arrDate = (string) $_POST['arrival_date'];
    $depDate = (string) $_POST['departure_date'];
    $transferCode = (string) trim(filter_var($_POST['transfer_code'], FILTER_SANITIZE_FULL_SPECIAL_CHARS));

    // Map features by ids
    $selectedFeatureIds = $_POST['feature_ids'] ?? [];
    $selectedFeatureIds = array_map('intval', $selectedFeatureIds);
    $selectedFeatures = getFeaturesById($selectedFeatureIds, $featuresInfo);

    // Calculate cost of booking
    $roomPrice = (int) calcRoomPrice($rooms, $roomId, $arrDate, $depDate);
    $featurePrice = (int) calcFeaturePrice($selectedFeatureIds, $featuresInfo);
    $totalCost = calcTotalCost($featurePrice, $roomPrice);

    // Get guest id for database
    $guestId = getOrAddGuest($database, $name, $guests);

    // Booked rooms for validation
    $bookedRooms = getBookedRooms($bookings, $arrDate);

    // ---- ROOM AVAILABILITY ----
    if (!isRoomAvailable($roomId, $bookedRooms)) {
        echo 'Sorry, chosen room is unavailable';
        return;
    }

    // ---- VALIDATE TRANSFERCODE ----
    $validationResponse = validateTransferCode($transferCode, $totalCost);
    echo 'VALIDATION RESPONSE: ';
    print_r($validationResponse);

    if (
        !isset($validationResponse['status'])
        && $validationResponse['status'] !== 'success'
    ) {
        echo $validationResponse['error'] ?? 'Transfer validation failed';
        return;
    }

    // ---- SEND RECEIPT TO CENTRALBANK ----
    $receiptResponse = sendReceipt(
        $hotelInfo,
        $apiKey,
        $name,
        $arrDate,
        $depDate,
        $selectedFeatures
    );
    echo 'Send Receipt response:';
    print_r($receiptResponse);

    if (
        !isset($receiptResponse['status'])
        && $receiptResponse['status'] !== 'success'
    ) {
        echo $receiptResponse['error'] ?? 'Receipt submission failed';
        return;
    }
    // ---- ADD BOOKING INTO DATABASE ----
    $bookingQuery = $database->prepare(
        'INSERT INTO room_bookings 
            (arrival_date, departure_date, room_id, guest_id, total_amount, amount_paid, feature_booking_id, transfer_code)
            VALUES (:arrival_date, :departure_date, :room_id, :guest_id, :total_amount, :amount_paid, :feature_booking_id, :transfer_code)'
    );

    $bookingQuery->execute([
        ':arrival_date' => $arrDate,
        ':departure_date' => $depDate,
        ':room_id' => $roomId,
        ':guest_id' => $guestId,
        ':total_amount' => $totalCost,
        ':amount_paid' => $totalCost,
        ':transfer_code' => $transferCode,
    ]);

    // DEPOSIT MONEY TO HOTEL OWNER
    $depositResponse = consumeTransferCode($hotelInfo, $transferCode);
    echo 'DEPOSIT RESPONSE:';
    print_r($depositResponse);

    echo 'Booking successfull';

    // ADD ANOTHER PAGE VIEW FOR SUCCESSFULL BOOKING
    // header('Location: /../index.php');
}
