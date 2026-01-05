<?php

declare(strict_types=1);

function groupFeatures(array $features)
{
    $groupedFeatures = [];

    foreach ($features as $feature) :
        $category = $feature['category'];
        $groupedFeatures[$category][] = $feature;
    endforeach;

    return $groupedFeatures;
}

function getFeatureNames(array $featureInfo): array
{
    $featureNames = [];
    foreach ($featureInfo as $feature) {
        $featureNames[] = $feature['name'];
    }
    return $featureNames;
}

function getFeaturesById(array $selectedFeatureIds, array $featuresInfo): array
{
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

function getOwnedFeatures(
    array $hotelInfo,
    string $apiKey
): array {
    $url = 'https://www.yrgopelag.se/centralbank/islandFeatures';
    $payload = json_encode([
        'user' => $hotelInfo['owner'],
        'api_key' => $apiKey,
    ]);

    $options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/json',
            'content' => $payload,
            // Allows 400 responses
            'ignore_errors' => true,
            'timeout' => 5
        ]
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    if ($response === false) {
        return ['error' => 'Feature request failed'];
    }

    $features = json_decode($response, true);
    $features = $features['features'];
    return $features;
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
