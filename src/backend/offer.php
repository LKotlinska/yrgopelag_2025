<?php
$offerId = (int) $_GET['offer_id'] ?? null;

$query = $database->prepare('SELECT name, preview_desc, image FROM offers WHERE offers.id = :offerId');
$query->execute([':offerId' => $offerId]);
$offer = $query->fetch(PDO::FETCH_ASSOC);

$query = $database->prepare(
    'SELECT
features.id,
features.name,
features.tier,
features.category,
features.price
FROM offer_feature
JOIN features ON offer_feature.feature_id = features.id
JOIN offers ON offer_feature.offer_id = offers.id
WHERE offers.id = :offerId'
);
$query->execute([':offerId' => $offerId]);
$offerSpecs = $query->fetchAll(PDO::FETCH_ASSOC);
