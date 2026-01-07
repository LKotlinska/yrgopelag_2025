<?php

declare(strict_types=1);

require __DIR__ . '/../database/data.php';

require __DIR__ . '/../functions/admin.functions.php';

require __DIR__ . '/../../vendor/autoload.php';

$query = $database->query(
    'SELECT * 
    FROM rooms'
);
$rooms = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query(
    'SELECT * 
    FROM features'
);
$featuresInfo = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query(
    'SELECT * 
    FROM hotel_info'
);
$hotelInfo = $query->fetchAll(PDO::FETCH_ASSOC);
$hotelInfo = $hotelInfo[0];


$query = $database->query(
    'SELECT * 
    FROM features 
    WHERE is_active = true'
);
$features = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query(
    'SELECT * 
    FROM offers 
    WHERE is_active = true'
);
$offers = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query(
    'SELECT features.id as f_id, 
    features.name as f_name, 
    offers.id as o_id, 
    offers.name as o_name
    FROM offer_feature
    JOIN features ON offer_feature.feature_id = features.id
    JOIN offers ON offer_feature.offer_id = offers.id'
);
$offerFeature = $query->fetchAll(PDO::FETCH_ASSOC);

$featureNames = (array) getFeatureNames($featuresInfo);
$activeFeatures = (array) getOwnedFeatures($hotelInfo, $apiKey);

foreach ($activeFeatures as $aFeature) {
    if (in_array($aFeature['feature'], $featureNames)) {
        $query = $database->prepare(
            'UPDATE features 
            SET is_active = true 
            WHERE name = :name'
        );
        $query->execute([':name' => $aFeature['feature']]);
    };
}

if (isset(
    $_POST['room_id'],
    $_POST['room_price']
)) {

    $roomId = $_POST['room_id'];
    $roomPrice = $_POST['room_price'];

    $updateRooms = $database->prepare(
        'UPDATE rooms 
        SET price_per_night = :price 
        WHERE id = :id'
    );
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

    $updateFeature = $database->prepare(
        'UPDATE features 
        SET price = :price 
        WHERE id = :id'
    );
    $updateFeature->execute([
        ':price' => $featurePrice,
        ':id' => $featureId
    ]);
    echo 'Success';
}

if (isset(
    $_POST['offer_action'],
    $_POST['offer_id'],
    $_POST['offer_name'],
    $_POST['offer_desc'],
    $_POST['offer_incl_desc'],
    $_POST['offer_incl_room'],
    $_POST['offer_img'],
    $_POST['offer_disc_type'],
    $_POST['offer_disc_value'],
    $_POST['offer_is']
)) {
    $actionType = $_POST['offer_action'];

    $data = [
        ':name' => trim($_POST['offer_name']),
        ':description' => trim($_POST['offer_desc']),
        ':included_desc' => trim($_POST['offer_incl_desc']),
        ':included_room' => trim($_POST['offer_incl_room']),
        ':image' => trim($_POST['offer_img']),
        ':discount_type' => trim($_POST['offer_disc_type']),
        ':discount_value' => (int) $_POST['offer_disc_value'],
        ':is_active' => (int) $_POST['offer_is'],
    ];

    if ($actionType === 'edit') {

        $data[':id'] = (int) $_POST['offer_id'];

        $query = $database->prepare(
            'UPDATE offers SET
                name = :name,
                description = :description,
                included_desc = :included_desc,
                included_room = :included_room,
                image = :image,
                discount_type = :discount_type,
                discount_value = :discount_value,
                is_active = :is_active
         WHERE id = :id'
        );
    } elseif ($actionType === 'add') {

        $query = $database->prepare(
            'INSERT INTO offers (
                name, 
                description, 
                included_desc, 
                included_room, 
                image, 
                discount_type, 
                discount_value, 
                is_active
            )
            VALUES(
                :name, 
                :description, 
                :included_desc, 
                :included_room, 
                :image, 
                :discount_type, 
                :discount_value, 
                :is_active
            )'
        );
    }
    $query->execute($data);
}

if (isset(
    $_POST['o_feature_id'],
    $_POST['f_offer_id'],
    $_POST['feature_action']
)) {
    $actionType = $_POST['feature_action'];
    $oFeatureId = (int) $_POST['o_feature_id'];
    $fOfferId   = (int) $_POST['f_offer_id'];

    $data = [
        ':offer_id' => $fOfferId,
        ':feature_id' => $oFeatureId
    ];

    if ($actionType === 'add') {
        $query = $database->prepare(
            'INSERT INTO offer_feature (offer_id, feature_id)
             VALUES (:offer_id, :feature_id)'
        );
        $query->execute($data);
        echo 'Successfully added feature to offer.';
    } elseif ($actionType === 'remove') {

        $query = $database->prepare(
            'DELETE FROM offer_feature
             WHERE offer_id = :offer_id
             AND feature_id = :feature_id'
        );
        $query->execute($data);

        echo 'Successfully removed feature from offer.';
    }
}
