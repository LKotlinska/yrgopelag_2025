<?php

declare(strict_types=1);

if (isset($_GET['offer_id'])) {
    $offerId = $_GET['offer_id'];
    $_SESSION['offer_id'] = $offerId;

    // Offer info for display - 
    $query = $database->prepare('SELECT name, description, image FROM offers WHERE offers.id = :offerId');
    $query->execute([':offerId' => $offerId]);
    $offer = $query->fetch(PDO::FETCH_ASSOC);

    $query = $database->prepare(
        'SELECT 
    features.name, 
    features.tier,
    features.price,
    features.category,
    features.id
    FROM offer_feature
    JOIN features ON offer_feature.feature_id = features.id
    JOIN offers ON offer_feature.offer_id = offers.id
    WHERE offers.id = :offerId'
    );

    $query->execute([':offerId' => $offerId]);
    $offerSpecs = $query->fetchAll(PDO::FETCH_ASSOC);

    $query = $database->prepare(
        'SELECT discount_value 
        FROM offers 
        WHERE id = :offerId'
    );

    $query->execute([
        ':offerId' => $offerId
    ]);

    $offerDiscount = $query->fetchColumn();;
}
