<?php

declare(strict_types=1);

// function getContent($url, $payload)
// {
//     $headers = ['Content-Type: application/json'];

//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//     curl_setopt($ch, CURLOPT_URL, $url);
//     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//     curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
//     /* FORCES HTTP1/1 due to curl error on the server */
//     // curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
//     $data = curl_exec($ch);

//     if ($data === false) {
//         unset($ch);

//         $ch = curl_init();
//         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//         curl_setopt($ch, CURLOPT_URL, $url);
//         curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//         curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
//         curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
//         $data = curl_exec($ch);
//         return $data;
//     }
//     // if ($data === false) {
//     //     file_put_contents('curl-log.txt', 'cURL error: ' . curl_error($ch), FILE_APPEND);
//     //     return false;
//     // }

//     $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

//     if ($httpCode !== 200) {
//         file_put_contents('http-log.txt', $httpCode, FILE_APPEND);
//         return false;
//     }

//     return $data;
// }


function getContent(string $url, string $payload)
{
    $headers = [
        'Content-Type: application/json',
        'Accept: application/json',
    ];

    $protocols = [
        CURL_HTTP_VERSION_2TLS, // preferred (works on localhost)
        CURL_HTTP_VERSION_1_1,  // fallback (fixes deploy)
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

        // 🔍 Log ALL non-200 responses INCLUDING BODY
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

function validateTransferCode(
    string $transferCode,
    int $totalPrice
): array {
    $url = 'https://www.yrgopelag.se/centralbank/transferCode';
    $payload = json_encode([
        'transferCode' => $transferCode,
        'totalCost' => $totalPrice,
    ]);

    // $headers = ['Content-Type: application/json'];
    // echo $payload;
    // $context = stream_context_create($headers);

    $response = getContent($url, $payload);

    file_put_contents('transfercode-logs.txt', date("H:i:s") . ' ' . microtime(), FILE_APPEND);
    file_put_contents('transfercode-logs.txt', 'Response: ' . $response . "\n", FILE_APPEND);
    // echo 'echo response' . $response;
    // print_r($response);

    // if ($response === false) {
    //     return ['error' => 'TransferCode validation request failed, please try again.'];
    // }

    $data = json_decode($response, true);

    if ($data === null) {
        return ['error' => '001 Invalid response from the server, please try again.'];
    }

    // echo 'echo data' . $data;
    // print_r($data);

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
    file_put_contents('receipt-logs.txt', date("H:i:s") . ' ' . microtime(), FILE_APPEND);
    file_put_contents('receipt-logs.txt', 'Response: ' . $response . "\n", FILE_APPEND);

    // if ($response === false) {
    //     return ['error' => 'Receipt request failed, please try again.'];
    // }

    $data = json_decode($response, true);

    if ($data === null) {
        return ['error' => '002 Invalid response from the server, please try again.'];
    }

    return $data;

    // $headers = [
    //     'http' => [
    //         'method' => 'POST',
    //         'header' => 'Content-Type: application/json',
    //         'content' => $payload,
    //         // Allows 400 responses
    //         'ignore_errors' => true,
    //         'timeout' => 5
    //     ]
    // ];
    // $context = stream_context_create($headers);
    // $response = file_get_contents($url, false, $context);

    // if ($response === false) {
    //     return ['error' => 'Receipt request failed'];
    // }
    // return json_decode($response, true);
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
    file_put_contents('deposit-logs.txt', date("H:i:s") . ' ' . microtime(), FILE_APPEND);
    file_put_contents('deposit-logs.txt', 'Response: ' . $response . "\n", FILE_APPEND);

    // if ($response === false) {
    //     return ['error' => 'Deposit request failed, please try again.'];
    // }

    $data = json_decode($response, true);

    if ($data === null) {
        return ['error' => '003 Invalid response from the server, please try again.'];
    }

    return $data;

    // $headers = [
    //     'http' => [
    //         'method' => 'POST',
    //         'header' => 'Content-Type: application/json',
    //         'content' => $payload,
    //         // Allows 400 responses
    //         'ignore_errors' => true,
    //         'timeout' => 5
    //     ]
    // ];
    // $context = stream_context_create($headers);
    // $response = file_get_contents($url, false, $context);

    // if ($response === false) {
    //     return ['error' => 'Deposit request failed'];
    // }
    // return json_decode($response, true);
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
    file_put_contents('withdraw-logs.txt', date("H:i:s") . ' ' . microtime(), FILE_APPEND);
    file_put_contents('withdraw-logs.txt', 'Response: ' . $response . "\n", FILE_APPEND);

    // if (!$response === false) {
    //     return ['error' => 'Withdraw request failed, please try again.'];
    // }

    $data = json_decode($response, true);

    if ($data === null) {
        return ['error' => '004 Invalid response from the server, please try again.'];
    }

    return $data;

    // $headers = [
    //     'http' => [
    //         'method' => 'POST',
    //         'header' => 'Content-Type: application/json',
    //         'content' => $payload,
    //         // Allows 400 responses
    //         'ignore_errors' => true,
    //         'timeout' => 5
    //     ]
    // ];

    // $context = stream_context_create($headers);
    // $response = file_get_contents($url, false, $context);
    // file_put_contents('withdraw-logs.txt', json_decode($response), FILE_APPEND);

    // if ($response === false) {
    //     return ['error' => 'Withdraw request failed'];
    // }
    // return json_decode($response, true);
};

function handleErrors(array $errors, int $roomId): void
{
    $errors = implode('£', $errors);
    // $_SESSION['errors'] = $errors;
    header("Location: ../../booking.php?id=$roomId&errors=$errors#error_msgs");
}

function handleBooking(
    PDO $database,
    array $hotelInfo,
    array $featuresInfo,
    array $bookings,
    array $guests,
    array $rooms,
    string $apiKey
): array {
    // Declare and sanitize
    $name = (string) trim(filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS));
    $roomId = (int) $_POST['room_id'];
    $arrDate = (string) $_POST['arrival_date'];
    $depDate = (string) $_POST['departure_date'];
    $guestKey = trim(filter_var($_POST['api_key'], FILTER_SANITIZE_FULL_SPECIAL_CHARS));

    // $name = 'Rune';
    // $roomId = 1;
    // $arrDate = '2025-01-27';
    // $depDate = '2025-01-28';
    // $guestKey = '10524e49-1955-484b-9368-9195f98bb7be';
    // $errors = [];

    // Map features by ids
    $selectedFeatureIds = $_POST['feature_ids'] ?? [];
    $selectedFeatureIds = array_map('intval', $selectedFeatureIds);
    $selectedFeatures = getFeaturesById($selectedFeatureIds, $featuresInfo);

    // $selectedFeatures = [];

    // Calculate cost of booking
    $roomPrice = (int) calcRoomPrice($rooms, $roomId, $arrDate, $depDate);
    $featurePrice = (int) calcFeaturePrice($selectedFeatureIds, $featuresInfo);
    $totalCost = calcTotalCost($featurePrice, $roomPrice);
    // $totalCost = 2;


    // Get guest id for database
    $guestId = getOrAddGuest($database, $name, $guests);
    // $guestId = 1;
    // Booked rooms for validation
    $bookedRooms = getBookedRooms($bookings, $arrDate, $depDate);

    // ---- ROOM AVAILABILITY ----
    if (!isRoomAvailable($roomId, $bookedRooms)) {
        $errors[] = 'This room is unavailable for the selected dates.';
        return $errors;
    }

    // ---- REQUEST WITHDRAW ----
    $withdrawResponse = requestWithdraw($name, $guestKey, $totalCost);
    if (
        isset($withdrawResponse['error']) ||
        !isset($withdrawResponse['transferCode'])
    ) {
        $errors[] = $withdrawResponse['error'] ?? "The withdrawal couldn't be processed. Please review your information and try again";
        return $errors;
        // exit;
    }
    // ---- VALIDATE TRANSFERCODE ----
    $transferCode = (string) $withdrawResponse['transferCode'];

    $validationResponse = validateTransferCode($transferCode, $totalCost);
    if (
        !isset($validationResponse['status']) ||
        $validationResponse['status'] !== 'success'
    ) {
        $errors[] = $validationResponse['error'] ?? 'Transfer validation failed, please try again.';
        return $errors;
        // exit;
    }

    if (empty($errors)) {

        // DEPOSIT MONEY TO HOTEL OWNER
        $depositResponse = consumeTransferCode($hotelInfo, $transferCode);
        if (
            !isset($depositResponse['status']) ||
            $depositResponse['status'] !== 'success'
        ) {
            $errors[] = $depositResponse['error'] ?? 'Deposit has failed.';
            return $errors;
            // exit;
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
        // echo 'Send Receipt response:';
        // print_r($receiptResponse);

        // file_put_contents('logs.txt', $receiptResponse, FILE_APPEND);
        if (
            !isset($receiptResponse['status']) ||
            $receiptResponse['status'] !== 'success'
        ) {
            $errors[] = $receiptResponse['error'] ?? 'Receipt submission failed';
            return $errors;

            // exit;
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

        $bookingId = (int)$database->lastInsertId();

        $_SESSION['booking_id'] = $bookingId;

        header('Location: ../../view/receipt.php');
    }

    return [];
}
