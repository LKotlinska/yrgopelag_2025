<?php require __DIR__ . '/../../src/controllers/calendar.php';

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
