<?php

declare(strict_types=1);

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
        return ['error' => 'TransferCode validation request failed'];
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
): array {
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
        return ['error' => 'Receipt request failed'];
    }
    return json_decode($response, true);
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
        return ['error' => 'Deposit request failed'];
    }
    return json_decode($response, true);
}
