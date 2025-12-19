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
