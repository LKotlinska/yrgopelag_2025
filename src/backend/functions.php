<?php

declare(strict_types=1);

function isExistingGuest(string $name, array $guests): bool
{
    foreach ($guests as $guest) {
        if ($name === $guest['name']) {
            return true;
        }
    }
    return false;
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
