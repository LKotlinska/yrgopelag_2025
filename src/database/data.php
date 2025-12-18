<?php
require __DIR__ . '/../backend/functions.php';

// ---- DOTENV LOAD

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
$dotenv->load();

$database = new PDO('sqlite:' . __DIR__ . '/../database/hotel.db');
// $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// $database->exec('PRAGMA foreign_keys = ON');

// ---- UPDATE DATABASE

$bookingQuery = $database->prepare(
    'INSERT INTO room_bookings 
    (arrival_date, departure_date, room_id, guest_id, total_amount, amount_paid, feature_booking_id, transfer_code)
    VALUES (:arrival_date, :departure_date, :room_id, :guest_id, :total_amount, :amount_paid, :feature_booking_id, :transfer_code)'
);

$addGuestQuery = $database->prepare('INSERT INTO guests (name) VALUES (:name)');

$addCategoriesQuery =  $database->prepare('INSERT OR IGNORE INTO feature_categories (category_name) VALUES (:category_name)');
$getCategoryId = $database->prepare('SELECT id FROM feature_categories WHERE category_name = :category_name');

$addTiersQuery = $database->prepare('INSERT OR IGNORE INTO feature_tiers (tier_name) VALUES (:tier_name)');
$getTierId = $database->prepare('SELECT id FROM feature_tiers WHERE tier_name = :tier_name');

$addFeaturesQuery =  $database->prepare('INSERT OR IGNORE INTO feature_names (feature_name, category_id, tier_id) VALUES (:feature_name, :category_id, :tier_id)');

// ---- DATABASE QUERIES AND FETCHES
$query = $database->query('SELECT * FROM feature_categories');
$featureCategories = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT * FROM feature_tiers');
$featureTiers = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT * FROM rooms');
$rooms = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT * FROM guests');
$guests = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT room_id, arrival_date, departure_date FROM room_bookings');
$bookings = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT * FROM hotel_info');
$hotelInfo = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT feature_name, price FROM feature_names JOIN feature_tiers ON feature_tiers.id = feature_names.tier_id');
$featuresInfo = $query->fetchAll(PDO::FETCH_ASSOC);

// FEATURE NAME SHOULD BE AN ID 
// ---- API FETCH
