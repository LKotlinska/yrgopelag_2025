<?php
require __DIR__ . '/../src/database/data.php';

$query = $database->query('SELECT * FROM features');
$featuresInfo = $query->fetchAll(PDO::FETCH_ASSOC);

?>
<form method="POST" id="booking-form" action="../src/backend/bookings.php">
    <label for="name">
        Enter your name:
    </label>
    <input type="text"
        id="name"
        name="name"
        required><br>

    <label for="room_id">
        Select room
    </label>
    <select
        name="room_id"
        id="room_id"
        required>
        <?php foreach ($rooms as $room) : ?>
            <option
                value="<?= $room['id']; ?>"
                data-price="<?= $room['price_per_night']; ?>">
                <?= $room['tier'] . ' $' . $room['price_per_night']; ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <label for="arrival_date">
        Arrival date:
    </label>
    <input
        type="date"
        id="arrival-date"
        name="arrival_date"
        min="2026-01-01"
        max="2026-01-31"
        required><br>

    <label for="departure_date">
        Departure date:
    </label>
    <input
        type="date"
        id="departure_date"
        name="departure_date"
        min="2026-01-01"
        max="2026-01-31"
        required><br>

    <label for="transfer_code">
        Enter your transferCode:
    </label>
    <input type="text"
        id="transfer_code"
        name="transfer_code"
        required><br>

    <?php foreach ($featuresInfo as $feature) : ?>
        <fieldset>
            <legend><?php echo $feature['category']; ?></legend>

            <input
                type="checkbox"
                name="feature_ids[]"
                id="feature_<?= $feature['id'] ?>"
                value="<?= $feature['id'] ?>">

            <label for="feature_<?= $feature['id'] ?>">
                <?php echo $feature['name']; ?> — $<?php echo $feature['price'] ?>
            </label>
        </fieldset>
    <?php endforeach; ?>

    <div>
        <h4>Total price:</h4>
        <p id="price"></p>
    </div>


    <button type="submit">Book</button>
</form>