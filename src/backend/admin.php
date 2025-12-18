<?php

declare(strict_types=1);

require __DIR__ . '/../database/data.php';

$query = $database->query('SELECT * FROM features');
$featuresInfo = $query->fetchAll(PDO::FETCH_ASSOC);

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

if (isset(
    $_POST['feature_id'],
    $_POST['feature_price']
)) {
    $featureId = $_POST['feature_id'];
    $featurePrice = $_POST['feature_price'];

    $updateFeature = $database->prepare('UPDATE features SET price = :price WHERE id = :id');
    $updateFeature->execute([
        ':price' => $featurePrice,
        ':id' => $featureId
    ]);
    echo 'Success';
}

if (isset(
    $_POST['update_features']
)) {
    $features = getActiveFeatures($hotelInfo, $_ENV['API_KEY']);
    activateFeatures($features, $addFeaturesQuery);
    echo 'Success';
}
