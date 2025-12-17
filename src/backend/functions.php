<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use GuzzleHttp\Client;

function isExistingGuest(string $name, array $guests): bool
{
    foreach ($guests as $guest) {
        if ($name === $guest['name']) {
            return true;
        }
    }
    return false;
}

function getGuestId(string $name, array $guests): int
{
    foreach ($guests as $guest) {
        if ($name === $guest['name']) {
            $guestId = $guest['id'];
            return $guestId;
        }
    }
    return -1;
}

function calculateRoomPrice(array $rooms, int $roomId, string $arrDate, string $depDate): int
{
    $roomPrice = $rooms[$roomId - 1]['price_per_night'];

    list($arrYear, $arrMonth, $arrDay) = explode("-", $arrDate);
    list($depYear, $depMonth, $depDay) = explode("-", $depDate);
    $nights = $depDay - $arrDay;

    $total = $roomPrice * $nights;

    return $total;
}

function getBookedRooms(array $bookings, string $date): array
{
    $unavailableRooms = [];
    foreach ($bookings as $booking) {
        if (
            $date >= $booking['arrival_date'] &&
            $date <  $booking['departure_date']
        ) {
            $unavailableRooms[] = [$date => $booking['room_id']];
        }
    }
    return $unavailableRooms;
}

function validateTransferCode(string $transferCode, int $totalPrice)
{
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
}
