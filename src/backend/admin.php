<?php

declare(strict_types=1);

require __DIR__ . '/../database/data.php';

if (isset(
    $_POST['id'],
    $_POST['price']
)) {

    $roomId = $_POST['id'];
    $roomPrice = $_POST['price'];

    $updateRooms = $database->prepare('UPDATE rooms SET price_per_night = :price WHERE id = :id');
    $updateRooms->bindParam(':id', $roomId);
    $updateRooms->bindParam(':price', $roomPrice);
    $updateRooms->execute();
    echo $roomId . $roomPrice;
}

if (isset(
    $_POST['update_features']
)) {
    $features = getActiveFeatures($hotelInfo, $_ENV['API_KEY']);
    activateFeatures($features, $addCategoriesQuery, $addFeaturesQuery, $addTiersQuery, $getCategoryId, $getTierId);
    echo 'Success';
}
