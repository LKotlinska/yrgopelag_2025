<?php

declare(strict_types=1);

function getContent(string $url, string $payload)
{
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
    ];

    $protocols = [
        // Works on localhost
        CURL_HTTP_VERSION_2TLS,
        // Fallback (fix for deployment)
        CURL_HTTP_VERSION_1_1,
    ];

    foreach ($protocols as $version) {
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_HTTP_VERSION   => $version,
            CURLOPT_TIMEOUT        => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
        ]);

        file_put_contents(
            __DIR__ . '/payload-log.txt',
            date('c') . "\n" . $payload . "\n\n",
            FILE_APPEND
        );

        $data     = curl_exec($ch);
        $errno    = curl_errno($ch);
        $error    = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        unset($ch);

        // Log all non-200 responses INCLUDING BODY
        if ($data === false || $httpCode !== 200) {
            file_put_contents(
                __DIR__ . '/curl-error-log.txt',
                sprintf(
                    "[%s]\nProtocol: %s\nHTTP: %d\ncURL errno: %d\ncURL error: %s\nResponse body:\n%s\n\n",
                    date('c'),
                    $version === CURL_HTTP_VERSION_2TLS ? 'HTTP/2' : 'HTTP/1.1',
                    $httpCode,
                    $errno,
                    $error ?: '(none)',
                    $data !== false ? $data : '(no body)'
                ),
                FILE_APPEND
            );
        }

        // Success path
        if ($data !== false && $httpCode === 200) {
            return $data;
        }

        // IMPORTANT: do NOT retry on business errors
        if ($httpCode >= 400 || $httpCode < 500) {
            return $data !== false ? $data : 'An unknown error has occurred';
        }
    }

    return 'No HTTP protocols defined for your request';
}

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

function isRoomAvailable(
    int $roomId,
    array $bookedRooms
): bool {
    return !in_array($roomId, $bookedRooms, true);
};

function validateTransferCode(
    string $transferCode,
    int $totalPrice
): array {
    $url = 'https://www.yrgopelag.se/centralbank/transferCode';
    $payload = json_encode([
        'transferCode' => $transferCode,
        'baseTotal' => $totalPrice,
    ]);

    $response = getContent($url, $payload);

    // file_put_contents('transfercode-logs.txt', date("H:i:s") . ' ' . microtime(), FILE_APPEND);
    // file_put_contents('transfercode-logs.txt', 'Response: ' . $response . "\n", FILE_APPEND);

    $data = json_decode($response, true);

    if ($data === null) {
        return ['error' => '001 Invalid response from the server, please try again.'];
    }

    return $data;
};

function sendReceipt(
    array $hotelInfo,
    string $apiKey,
    string $name,
    string $arrDate,
    string $depDate,
    array $selectedFeatures
) {
    $url = 'https://www.yrgopelag.se/centralbank/receipt';
    $payload = json_encode([
        'user' => $hotelInfo['owner'],
        'api_key' => $apiKey,
        'guest_name' => $name,
        'arrival_date' => $arrDate,
        'departure_date' => $depDate,
        'features_used' => $selectedFeatures,
        'star_rating' => (int) $hotelInfo['stars'],
    ]);

    $response = getContent($url, $payload);

    // file_put_contents('receipt-logs.txt', date("H:i:s") . ' ' . microtime(), FILE_APPEND);
    // file_put_contents('receipt-logs.txt', 'Response: ' . $response . "\n", FILE_APPEND);

    $data = json_decode($response, true);

    if ($data === null) {
        return ['error' => '002 Invalid response from the server, please try again.'];
    }

    return $data;
}


function consumeTransferCode(
    array $hotelInfo,
    string $transferCode
): array {
    $url = 'https://www.yrgopelag.se/centralbank/deposit';
    $payload = json_encode([
        'user' => $hotelInfo['owner'],
        'transferCode' => $transferCode,
    ]);


    $response = getContent($url, $payload);

    // file_put_contents('deposit-logs.txt', date("H:i:s") . ' ' . microtime(), FILE_APPEND);
    // file_put_contents('deposit-logs.txt', 'Response: ' . $response . "\n", FILE_APPEND);

    $data = json_decode($response, true);

    if ($data === null) {
        return ['error' => '003 Invalid response from the server, please try again.'];
    }

    return $data;
}

function requestWithdraw(
    string $name,
    string $guestKey,
    int $amount
): array {
    $url = 'https://www.yrgopelag.se/centralbank/withdraw';
    $payload = json_encode([
        'user' => $name,
        'api_key' => $guestKey,
        'amount' => $amount
    ]);

    $response = getContent($url, $payload);

    // file_put_contents('withdraw-logs.txt', date("H:i:s") . ' ' . microtime(), FILE_APPEND);
    // file_put_contents('withdraw-logs.txt', 'Response: ' . $response . "\n", FILE_APPEND);

    $data = json_decode($response, true);

    if ($data === null) {
        return ['error' => '004 Invalid response from the server, please try again.'];
    }

    return $data;
};

function handleBooking(
    PDO $database,
    array $hotelInfo,
    array $featuresInfo,
    array $bookings,
    array $guests,
    array $rooms,
    string $apiKey
): array {
    // Declare and sanitize input
    $offerId = isset($_POST['offer_id']) ? (int) $_POST['offer_id'] : null;
    $name = (string) trim(filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $roomId = (int) $_POST['room_id'];
    $arrDate = (string) $_POST['arrival_date'];
    $depDate = (string) $_POST['departure_date'];
    $paymentMethod = $_POST['payment_method'] ?? '';
    $guestKey = null;
    $transferCode = null;

    // Map features by ids
    $selectedFeatureIds = $_POST['feature_ids'] ?? [];
    $selectedFeatureIds = array_map('intval', $selectedFeatureIds);
    $selectedFeatures = getFeatureNameById($selectedFeatureIds, $featuresInfo);

    // Calculate cost of booking
    $roomPrice = (int) calcRoomPrice($rooms, $roomId, $arrDate, $depDate);
    $featurePrice = (int) calcFeaturePrice($selectedFeatureIds, $featuresInfo);
    $baseTotal = calcTotalCost($featurePrice, $roomPrice);

    // Handle discount for offers
    $discount = 0;
    if ($offerId !== null) {
        $query = $database->prepare(
            'SELECT discount_value 
            FROM offers 
            WHERE id = :offer_id'
        );
        $query->execute([':offer_id' => $offerId]);
        $offer = $query->fetch(PDO::FETCH_ASSOC);
        $discount = (int) $offer['discount_value'];
    }

    $totalCost = applyDiscount($baseTotal, $discount);

    // Get guest id for database
    $guestId = getOrAddGuest($database, $name, $guests);

    $bookedRooms = getBookedRooms($bookings, $arrDate, $depDate);

    $errors = [];

    // ---- CHECKS ROOM AVAILABILITY ----
    if (!isRoomAvailable($roomId, $bookedRooms)) {
        $errors[] = 'This room is unavailable for the selected dates.';
        return [
            'success' => false,
            'errors' => $errors,
            'room_id' => $roomId,
            'offer_id' => $offerId
        ];
    }

    // ---- HANDLES DECLARED PAYMENT METHOD LOGIC ---- 

    // ---- API KEY
    if ($paymentMethod === 'api_key') {

        $guestKey = trim(
            filter_var($_POST['api_key'], FILTER_SANITIZE_FULL_SPECIAL_CHARS)
        );

        // ---- REQUEST WITHDRAW ----
        $withdrawResponse = requestWithdraw($name, $guestKey, $baseTotal);

        if (
            isset($withdrawResponse['error']) ||
            !isset($withdrawResponse['transferCode'])
        ) {
            $errors[] = $withdrawResponse['error'] ?? "The withdrawal couldn't be processed. Please review your information and try again";
            return [
                'success' => false,
                'errors' => $errors,
                'room_id' => $roomId,
                'offer_id' => $offerId
            ];
        };

        // Since it came from the server, no need to validate
        $transferCode = (string) $withdrawResponse['transferCode'];
    }

    // ---- TRANFERCODE ----
    if ($paymentMethod === 'transfer_code') {
        $transferCode = (string) $_POST['transfer_code'];
        if (empty($_POST['transfer_code'])) {
            $errors[] = 'Transfer code is required.';
            return [
                'success' => false,
                'errors' => $errors,
                'room_id' => $roomId,
                'offer_id' => $offerId
            ];
        }

        // ---- VALIDATE TRANSFERCODE ----
        $validationResponse = validateTransferCode($transferCode, $baseTotal);

        if (
            !isset($validationResponse['status']) ||
            $validationResponse['status'] !== 'success'
        ) {
            $errors[] = $validationResponse['error'] ?? 'Transfer validation failed, please try again.';
            return [
                'success' => false,
                'errors' => $errors,
                'room_id' => $roomId,
                'offer_id' => $offerId
            ];
        }
    }

    if (empty($errors)) {
        // ---- DEPOSIT MONEY TO HOTEL OWNER
        // Breaks the recommended flow of the page, as deposit can still fail
        $depositResponse = consumeTransferCode($hotelInfo, $transferCode);
        if (
            !isset($depositResponse['status']) ||
            $depositResponse['status'] !== 'success'
        ) {
            $errors[] = $depositResponse['error'] ?? 'Deposit has failed.';
            return [
                'success' => false,
                'errors' => $errors,
                'room_id' => $roomId,
                'offer_id' => $offerId
            ];
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

        if (
            !isset($receiptResponse['status']) ||
            $receiptResponse['status'] !== 'success'
        ) {
            $errors[] = $receiptResponse['error'] ?? 'Receipt submission failed';
            return [
                'success' => false,
                'errors' => $errors,
                'room_id' => $roomId,
                'offer_id' => $offerId
            ];
        }

        // ---- ADD BOOKING TO DATABASE ----
        $addBookingReceipt = $database->prepare(
            'INSERT INTO booking_receipt 
                    (arrival_date, departure_date, room_id, guest_id, amount_paid, transfer_code)
                    VALUES (:arrival_date, :departure_date, :room_id, :guest_id, :amount_paid, :transfer_code)'
        );

        $addBookingReceipt->execute([
            ':arrival_date' => $arrDate,
            ':departure_date' => $depDate,
            ':room_id' => $roomId,
            ':guest_id' => $guestId,
            ':amount_paid' => $totalCost,
            ':transfer_code' => $transferCode,
        ]);

        $bookingId = (int)$database->lastInsertId();

        // Add to database -> features_bookings table
        insertFeatures($database, $selectedFeatureIds, $bookingId);

        // Add to database if offer was booked -> offer_features_bookings table
        if ($offerId != null) {
            insertOfferFeatureBooking($database, $selectedFeatureIds, $bookingId, $offerId);
        }

        // Store booking_id for receipt
        $_SESSION['booking_id'] = $bookingId;

        return [
            'success' => true,
            'booking_id' => $bookingId
        ];
    }

    // Failed

    return [
        'success' => false,
        'errors' => $errors,
        'room_id' => $roomId,
        'offer_id' => $offerId
    ];
}
