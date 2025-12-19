<?php

declare(strict_types=1);

function isExistingGuest(
    string $name,
    array $guests
): bool {
    foreach ($guests as $guest) {
        if ($name === $guest['name']) {
            return true;
        }
    }
    return false;
};

function getGuestId(
    string $name,
    array $guests
): int {
    foreach ($guests as $guest) {
        if ($name === $guest['name']) {
            $guestId = $guest['id'];
            return $guestId;
        }
    }
    return -1;
};

function calcRoomPrice(
    array $rooms,
    int $roomId,
    string $arrDate,
    string $depDate
): int {
    $roomPrice = $rooms[$roomId - 1]['price_per_night'];

    list($arrYear, $arrMonth, $arrDay) = explode("-", $arrDate);
    list($depYear, $depMonth, $depDay) = explode("-", $depDate);
    $nights = $depDay - $arrDay;

    $total = $roomPrice * $nights;

    return $total;
};

function calcFeaturePrice(array $selectedFeatureIds, array $featuresInfo): int
{
    $total = 0;
    foreach ($featuresInfo as $feature) {
        if (in_array($feature['id'], $selectedFeatureIds, true)) {
            $total += (int) $feature['price'];
        }
    }
    return $total;
}

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

function validateTransferCode(
    string $transferCode,
    int $totalPrice
): array {
    $url = 'https://www.yrgopelag.se/centralbank/transferCode';
    $payload = json_encode([
        'transferCode' => $transferCode,
        'totalCost' => $totalPrice,
    ]);

    $options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => $payload,
            // Allows 400 responses
            'ignore_errors' => true
        ]
    ];

    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === false) {
        return ['error' => 'Request failed'];
    }
    return json_decode($response, true);
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

    $options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => $payload,
            // Allows 400 responses
            'ignore_errors' => true
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === false) {
        return ['error' => 'Request failed'];
    }
    print_r($selectedFeatures);

    return json_decode($response, true);
}

function getOwnedFeatures(
    array $hotelInfo,
    string $apiKey
): array {
    $url = 'https://www.yrgopelag.se/centralbank/islandFeatures';
    $payload = json_encode([
        'user' => $hotelInfo['owner'],
        'api_key' => $apiKey,
    ]);

    $options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => $payload,
            // Allows 400 responses
            'ignore_errors' => true
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    if ($response === false) {
        return ['error' => 'Request failed'];
    }

    $features = json_decode($response, true);
    $features = $features['features'];
    return $features;
}

function activateFeatures(
    array $features,
    PDOStatement $addFeaturesQuery,
): void {
    foreach ($features as $feature) {
        $addFeaturesQuery->execute([
            ':name' => $feature['feature'],
            ':category' => $feature['activity'],
            ':tier' => $feature['tier'],
        ]);
    };
}

function getFeaturesById(array $selectedFeatureIds, array $featuresInfo): array
{
    if (empty($selectedFeatureIds)) {
        echo 'Empty variable';
        return [];
    }

    $matchedFeatures = [];

    // WILL NEED TO BE ADJUSTED WHEN I PURCHASE HOTEL-SPECIFIC
    foreach ($featuresInfo as $feature) {
        if (in_array($feature['id'], $selectedFeatureIds, true)) {
            print_r($feature);
            $matchedFeatures[] = [
                'activity' => $feature['category'],
                'tier' => $feature['tier']
            ];
        }
    };
    return $matchedFeatures;
}

function consumeTransferCode(array $hotelInfo, string $transferCode)
{
    $url = 'https://www.yrgopelag.se/centralbank/deposit';
    $payload = json_encode([
        'user' => $hotelInfo['owner'],
        'transferCode' => $transferCode,
    ]);

    $options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => $payload,
            // Allows 400 responses
            'ignore_errors' => true
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === false) {
        return ['error' => 'Request failed'];
    }
    return json_decode($response, true);
}
