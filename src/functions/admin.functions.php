<?php

declare(strict_types=1);

function getFeatureNames(
    array $featureInfo
): array {
    $featureNames = [];
    foreach ($featureInfo as $feature) {
        $featureNames[] = $feature['name'];
    }
    return $featureNames;
}

function handleLogin(
    string $username,
    string $password,
    string $adminUsername,
    string $adminPassword
): array {
    if ($username === $adminUsername && password_verify($password, $adminPassword)) {
        return [
            'success' => true,
        ];
    } else {
        $errors = [];
        $errors[] = 'Incorrect username or password.';
        return [
            'success' => false,
            'errors' => $errors,
        ];
    }
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
