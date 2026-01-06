<?php
require __DIR__ . '/../../src/database/data.php';

?>
<form method="POST" id="booking-form" action="../src/backend/booking.submit.php">

    <!-- Date selection -->
    <?php require __DIR__ . '/form/dates.php'; ?>

    <!-- Features selection -->
    <?php if (isset($offerId) && !empty($offerId)) {
        require __DIR__ . '/../../src/backend/offer.php';
    }; ?>

    <?php require __DIR__ . '/form/features.php'; ?>

    <!-- Guest information field -->
    <?php require __DIR__ . '/form/guest.php'; ?>

    <!-- Room information -->
    <input
        type="hidden"
        id="room_id"
        name="room_id"
        value="<?php echo $room['id']; ?>">

    <!-- Hidden input in case of offer features that are disabled! -->
    <?php if (isset($offerId) && !empty($offerId)) { ?>
        <input
            type="hidden"
            id="offer_id"
            name="offer_id"
            value="<?php echo $offerId; ?>">
    <?php } ?>

    <?php if (isset($offerId)) : ?>
        <input type="hidden" name="offer_id" value="<?= $offerId ?>">
    <?php endif; ?>

    <!-- Payment fields -->
    <?php require __DIR__ . '/form/payment.php'; ?>
</form>