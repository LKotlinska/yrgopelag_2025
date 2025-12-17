<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

require __DIR__ . '/src/database/data.php';

require __DIR__ . '/src/backend/functions.php';

require __DIR__ . '/view/header.php';

require __DIR__ . '/view/form.php';

require __DIR__ . '/view/calendar.php';

require __DIR__ . '/view/footer.php';

function calculateRoomPrice($rooms, $roomId, $arrDate, $depDate)
{
    $roomPrice = $rooms[$roomId - 1]['price_per_night'];

    list($arrYear, $arrMonth, $arrDay) = explode("-", $arrDate);
    list($depYear, $depMonth, $depDay) = explode("-", $depDate);
    $nights = $depDay - $arrDay;

    $total = $roomPrice * $nights;

    return $total;
}
