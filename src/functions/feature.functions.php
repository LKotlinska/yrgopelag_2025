<?php

declare(strict_types=1);

function groupFeatures(
    array $features
): array {
    $groupedFeatures = [];

    foreach ($features as $feature) :
        $category = $feature['category'];
        $groupedFeatures[$category][] = $feature;
    endforeach;

    return $groupedFeatures;
}

function getFeatureNameById(
    array $selectedFeatureIds,
    array $featuresInfo
): array {
    if (empty($selectedFeatureIds)) {
        return [];
    }

    $matchedFeatures = [];

    // WILL NEED TO BE ADJUSTED WHEN I PURCHASE HOTEL-SPECIFIC
    foreach ($featuresInfo as $feature) {
        if (in_array($feature['id'], $selectedFeatureIds, true)) {
            $matchedFeatures[] = [
                'activity' => $feature['category'],
                'tier' => $feature['tier']
            ];
        }
    };
    return $matchedFeatures;
}

function insertFeatures(
    PDO $database,
    array $selectedFeatureIds,
    int $bookingId,
): void {
    $query = $database->prepare(
        'INSERT INTO booking_features (booking_id, feature_id)
         VALUES (:booking_id, :feature_id)'
    );
    foreach ($selectedFeatureIds as $featureId) :
        $query->execute([
            ':booking_id' => (int) $bookingId,
            ':feature_id' => (int) $featureId
        ]);
    endforeach;
}

function insertOfferFeatureBooking(
    PDO $database,
    array $selectedFeatureIds,
    int $bookingId,
    int $offerId
): void {
    $query = $database->prepare(
        'INSERT INTO offer_feature_booking (offer_id, feature_id, booking_id)
        VALUES (:offer_id, :feature_id, :booking_id)'
    );
    foreach ($selectedFeatureIds as $featureId) :
        $query->execute([
            ':offer_id' => (int) $offerId,
            ':feature_id' => (int) $featureId,
            ':booking_id' => (int) $bookingId
        ]);
    endforeach;
}

function activateFeatures(
    array $features,
    PDO $database
): void {
    $addFeaturesQuery = $database->prepare('INSERT OR IGNORE INTO features (name, category, tier) VALUES (:name, :category, :tier)');
    foreach ($features as $feature) {
        $addFeaturesQuery->execute([
            ':name' => $feature['feature'],
            ':category' => $feature['activity'],
            ':tier' => $feature['tier'],
        ]);
    };
}
