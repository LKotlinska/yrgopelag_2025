<?php

declare(strict_types=1);

require __DIR__ . '/../functions/calendar.functions.php';

$query = $database->query('SELECT room_id, arrival_date, departure_date FROM booking_receipts');
$bookings = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT * FROM rooms');
$rooms = $query->fetchAll(PDO::FETCH_ASSOC);

$roomId = $_GET['room_id'];

// Hardcoded due to task limitation
$year = 2026;
$month = 1;
$day = 1;
$cells = 42;

$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$firstDayOfMonth = date('N', strtotime("$year-$month-01"));
