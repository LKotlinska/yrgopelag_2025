<?php
// Hardcoded due to task limitation
$year = 2026;
$month = 1;
$day = 1;
$cells = 42;

$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$firstDayOfMonth = date('N', strtotime("$year-$month-01"));
$date = sprintf('%04d-01-%02d', $year, $day);

function bookedRooms(array $bookings, string $date): array
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
