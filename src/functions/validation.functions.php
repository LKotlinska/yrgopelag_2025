<?php

declare(strict_types=1);

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

function handleErrors(array $errors, int $roomId): void
{
    $_SESSION['errors'] = $errors;
    header("Location: /booking.php?id=$roomId#error_msgs");
}
