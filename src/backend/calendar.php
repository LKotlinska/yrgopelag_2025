<?php

declare(strict_types=1);

require __DIR__ . '/functions.php';

// Hardcoded due to task limitation
$year = 2026;
$month = 1;
$day = 1;
$cells = 42;

$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
$firstDayOfMonth = date('N', strtotime("$year-$month-01"));
