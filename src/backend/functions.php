<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

// Hardcoded due to task limitation

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

function calculateRoomPrice(
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
    string $name,
    string $arrDate
) {
    $url = 'https://www.yrgopelag.se/centralbank/receipt';
    $payload = json_encode([
        // 'user' => ,
        // 'api_key' => ,
        // 'island_id' => WHERE TO GET THAT??? ,
        // 'guest_name' => $name,
        // 'arrival_date' => $arrDate,
        // 'departure_date' => $depDate,
        // 'features_used' => [],
        // 'star_rating' => .
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

function getActiveFeatures(
    array $hotelInfo,
    string $apiKey
): array {
    $url = 'https://www.yrgopelag.se/centralbank/islandFeatures';
    $payload = json_encode([
        'user' => $hotelInfo[0]['owner'],
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
    PDOStatement $addCategoriesQuery,
    PDOStatement $addFeaturesQuery,
    PDOStatement $addTiersQuery,
    PDOStatement $getCategoryId,
    PDOStatement $getTierId
): void {
    foreach ($features as $feature) {
        $addCategoriesQuery->execute([
            ':category_name' => $feature['activity'],
        ]);
        $categoryId = $getCategoryId->execute([
            ':category_name' => $feature['activity'],
        ]);
        $addTiersQuery->execute([
            ':tier_name' => $feature['tier'],
        ]);
        $tierId = $getTierId->execute([
            ':tier_name' => $feature['tier'],
        ]);
        $addFeaturesQuery->execute([
            ':feature_name' => $feature['feature'],
            ':category_id' => $categoryId,
            ':tier_id' => $tierId,
        ]);
    };
}
