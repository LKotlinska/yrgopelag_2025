<?php

require __DIR__ . '/../src/database/data.php';

$roomId = (int) $_GET['room_id'];

$query = $database->prepare('SELECT * FROM rooms WHERE id = :roomId');
$query->execute([':roomId' => $roomId,]);
$room = $query->fetch(PDO::FETCH_ASSOC);

$query = $database->query('SELECT * FROM room_amenity JOIN amenities ON room_amenity.amenity_id = amenities.id');
$amenities = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT * FROM features');
$featuresInfo = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT * FROM rooms');
$rooms = $query->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['offer_id'])) {
    $offerId = (int) $_GET['offer_id'];
    $query = $database->prepare('SELECT name, preview_desc, image FROM offers WHERE offers.id = :offerId');
    $query->execute([':offerId' => $offerId]);
    $offer = $query->fetch(PDO::FETCH_ASSOC);

    $query = $database->prepare(
        'SELECT 
    features.name, 
    features.tier,
    features.price
    FROM offer_feature
    JOIN features ON offer_feature.feature_id = features.id
    JOIN offers ON offer_feature.offer_id = offers.id
    WHERE offers.id = :offerId'
    );
    $query->execute([':offerId' => $offerId]);
    $offerSpecs = $query->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">

<?php require __DIR__ . '/metadata/head.php'; ?>
<script src="../src/scripts/booking.js"></script>

<body>
    <img class="sub-bg" src="../assets/images/terracotta-hotel.png">
    <main>
        <section class="booking-section">
            <?php require __DIR__ . '/components/calendar.php'; ?>
            <article class="booking-container">
                <header class="booking-header">
                    <figure>
                        <img class="booking-img" src="../assets/images/<?php echo $room['room_image']; ?>">
                    </figure>
                    <h1>
                        <?php echo $room['tier'] ?> room
                    </h1>
                    <span>
                        Cost per night: $<span id="price-per-night" data-price="<?php echo $room['price_per_night']; ?>"><?php echo $room['price_per_night']; ?></span>
                    </span>
                </header>
                <div class="booking-body">
                    <div class="booking-desc">
                        <p>
                            <?php echo $room['description']; ?>
                        </p>
                        <?php require __DIR__ . '/components/amenities.php'; ?>
                    </div>

                    <?php if (isset($_GET['errors'])) {
                        $errors = $_GET['errors'];
                        $errors = explode('£', $errors); ?>
                        <div id="error_msgs">
                            <?php foreach ($errors as $error) : ?>
                                <div class="msg-card error-s">
                                    <p>
                                        <span class="material-symbols-outlined">
                                            error
                                        </span><?php echo $error; ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php } ?>

                    <?php require __DIR__ . '/components/booking-form.php'; ?>

                </div>
            </article>
        </section>
    </main>


    <?php require __DIR__ . '/components/footer.php'; ?>