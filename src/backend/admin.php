<?php

declare(strict_types=1);

require __DIR__ . '/../database/data.php';

require __DIR__ . '/../functions/feature.functions.php';

require __DIR__ . '/../../vendor/autoload.php';

$query = $database->query('SELECT * FROM rooms');
$rooms = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT * FROM features');
$featuresInfo = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT * FROM hotel_info');
$hotelInfo = $query->fetchAll(PDO::FETCH_ASSOC);
$hotelInfo = $hotelInfo[0];

$featureNames = (array) getFeatureNames($featuresInfo);
$activeFeatures = (array) getOwnedFeatures($hotelInfo, $apiKey);

$query = $database->query('SELECT * FROM features WHERE is_active = true');
$features = $query->fetchAll(PDO::FETCH_ASSOC);

foreach ($activeFeatures as $aFeature) {
    if (in_array($aFeature['feature'], $featureNames)) {
        $query = $database->prepare('UPDATE features SET is_active = true WHERE name = :name');
        $query->execute([':name' => $aFeature['feature']]);
    };
}

if (isset(
    $_POST['room_id'],
    $_POST['room_price']
)) {

    $roomId = $_POST['room_id'];
    $roomPrice = $_POST['room_price'];

    $updateRooms = $database->prepare('UPDATE rooms SET price_per_night = :price WHERE id = :id');
    $updateRooms->execute([
        ':price' => $roomPrice,
        ':id' => $roomId
    ]);
    echo 'Success';
}

// if (isset(
//     $_POST['feature_id'],
//     $_POST['feature_price']
// )) {
//     $featureId = $_POST['feature_id'];
//     $featurePrice = $_POST['feature_price'];

//     $updateFeature = $database->prepare('UPDATE features SET price = :price WHERE id = :id');
//     $updateFeature->execute([
//         ':price' => $featurePrice,
//         ':id' => $featureId
//     ]);
//     echo 'Success';
// }
