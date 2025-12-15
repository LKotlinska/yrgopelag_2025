<?php

$database = new PDO('sqlite:' . __DIR__ . '/../database/hotel.db');

$year = 2026;
$month = 1;
$day = 1;
$cells = 42;
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);


$firstDayOfMonth = date('N', strtotime("$year-$month-01"));



$availability = [];

if (isset($_POST['selected_date'])) {
    $selectedDate = $_POST['selected_date'];
    $query = $database->prepare('SELECT room_id, is_available 
                                 FROM room_availability 
                                 WHERE date = :date');

    $query->execute([':date' => $selectedDate]);
    $availability = $query->fetchAll(PDO::FETCH_ASSOC);
    print_r($availability);
}
