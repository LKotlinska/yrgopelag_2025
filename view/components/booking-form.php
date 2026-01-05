<?php
require __DIR__ . '/../../src/database/data.php';

if (isset($offerId)) {
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
}


?>
<form method="POST" id="booking-form" action="../src/backend/bookings.php">
    <!-- Date selection -->
    <?php require __DIR__ . '/form/dates.php'; ?>
    <!-- Features selection -->

    <?php
    if (isset($offerId)) :
        require __DIR__ . '/../../src/backend/offer.php';
    endif; ?>

    <?php require __DIR__ . '/form/features.php'; ?>

    <!-- Guest information field -->
    <?php require __DIR__ . '/form/guest.php'; ?>

    <!-- Room information -->
    <input
        type="hidden"
        id="room_id"
        name="room_id"
        value="<?php echo $room['id']; ?>">
    <?php if ($offerId) { ?>
        <input
            type="hidden"
            id="offer_id"
            name="offer_id"
            value="<?php echo $offerId; ?>">
    <?php } ?>
    <!-- Payment fields -->
    <?php require __DIR__ . '/form/payment.php'; ?>
</form>