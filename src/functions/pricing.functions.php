<?php

declare(strict_types=1);

function calcRoomPrice(
    array $rooms,
    int $roomId,
    string $arrDate,
    string $depDate
): int {
    $roomPrice = $rooms[$roomId - 1]['price_per_night'];

    list($arrYear, $arrMonth, $arrDay) = explode("-", $arrDate);
    list($depYear, $depMonth, $depDay) = explode("-", $depDate);
    $nights = $depDay - $arrDay;

    $total = $roomPrice * $nights;

    return $total;
};

function calcFeaturePrice(
    array $selectedFeatureIds,
    array $featuresInfo
): int {
    $total = 0;
    foreach ($featuresInfo as $feature) {
        if (in_array($feature['id'], $selectedFeatureIds, true)) {
            $total += (int) $feature['price'];
        }
    }
    return $total;
}

function calcTotalCost(
    int $featureCost,
    int $roomCost
): int {
    $total = $featureCost + $roomCost;
    return $total;
}

function applyDiscount(
    int $totalCost,
    int $discount
): int {
    $newCost = $totalCost - $discount;
    return $newCost;
}
