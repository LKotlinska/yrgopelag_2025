<?php

declare(strict_types=1);

function getBookedRooms(
    array $bookings,
    string $arrival,
    string $departure
): array {
    $unavailableRooms = [];

    foreach ($bookings as $booking) {
        if (
            $arrival < $booking['departure_date'] &&
            $departure > $booking['arrival_date']
        ) {
            $unavailableRooms[] = (int) $booking['room_id'];
        }
    }

    return $unavailableRooms;
}
function isRoomBooked(array $bookings, int $roomId, string $date): bool
{
    foreach ($bookings as $booking) {
        if (
            (int) $booking['room_id'] === $roomId &&
            $date >= $booking['arrival_date'] &&
            $date < $booking['departure_date']
        ) {
            return true;
        }
    }
    return false;
}

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
): void {
    // Declare and sanitize
    $name = (string) trim(filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $roomId = (int) $_POST['room_id'];
    $arrDate = (string) $_POST['arrival_date'];
    $depDate = (string) $_POST['departure_date'];
    $guestKey = trim(filter_var($_POST['api_key'], FILTER_SANITIZE_FULL_SPECIAL_CHARS));


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
    $bookedRooms = getBookedRooms($bookings, $arrDate, $depDate);

    // ---- ROOM AVAILABILITY ----
    if (!isRoomAvailable($roomId, $bookedRooms)) {
        $errors[] = 'This room is unavailable for the selected dates.';
        handleErrors($errors, $roomId);
        exit;
    }

    // ---- REQUEST WITHDRAW ----
    $withdrawResponse = requestWithdraw($name, $guestKey, $totalCost);
    if (
        isset($withdrawResponse['error']) ||
        !isset($withdrawResponse['transferCode'])
    ) {
        $errors[] = $withdrawResponse['error'] ?? "The withdrawal couldn't be processed. Please review your information and try again";
        handleErrors($errors, $roomId);
        exit;
    } else {
        // ---- VALIDATE TRANSFERCODE ----
        $transferCode = (string) $withdrawResponse['transferCode'];
        $validationResponse = validateTransferCode($transferCode, $totalCost);
        if (
            !isset($validationResponse['status']) ||
            $validationResponse['status'] !== 'success'
        ) {
            $errors[] = $validationResponse['error'] ?? 'Transfer validation failed, please try again.';
            handleErrors($errors, $roomId);
            exit;
        }
    }

    // ---- SEND RECEIPT TO CENTRALBANK ----
    if (empty($errors)) {

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
            !isset($receiptResponse['status']) ||
            $receiptResponse['status'] !== 'success'
        ) {
            $errors[] = $receiptResponse['error'] ?? 'Receipt submission failed';
            handleErrors($errors, $roomId);
            exit;
        }

        // ---- ADD BOOKING INTO DATABASE ----
        $bookingQuery = $database->prepare(
            'INSERT INTO room_bookings 
                (arrival_date, departure_date, room_id, guest_id, amount_paid, transfer_code)
                VALUES (:arrival_date, :departure_date, :room_id, :guest_id, :amount_paid, :transfer_code)'
        );

        $bookingQuery->execute([
            ':arrival_date' => $arrDate,
            ':departure_date' => $depDate,
            ':room_id' => $roomId,
            ':guest_id' => $guestId,
            ':amount_paid' => $totalCost,
            ':transfer_code' => $transferCode,
        ]);

        // DEPOSIT MONEY TO HOTEL OWNER
        $depositResponse = consumeTransferCode($hotelInfo, $transferCode);
        if (
            !isset($depositResponse['status']) ||
            $depositResponse['status'] !== 'success'
        ) {
            $errors[] = $depositResponse['error'] ?? 'Deposit has failed.';
            handleErrors($errors, $roomId);
            exit;
        }

        $bookingId = (int)$database->lastInsertId();

        $_SESSION['booking_id'] = $bookingId;

        header('Location: /../view/receipt.php');
    }
}
