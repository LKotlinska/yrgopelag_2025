<?php require __DIR__ . '/../../src/backend/calendar.php';

renderCalendar(
    $cells,
    $firstDayOfMonth,
    $daysInMonth,
    $day,
    $year,
    $month,
    $bookings,
    $roomId
);
