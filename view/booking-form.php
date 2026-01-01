<?php
require __DIR__ . '/../src/database/data.php';

$query = $database->query('SELECT * FROM features');
$featuresInfo = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT * FROM rooms');
$rooms = $query->fetchAll(PDO::FETCH_ASSOC);

?>
<form method="POST" id="booking-form" action="../src/backend/bookings.php">
    <h3>Dates</h3>
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
    <p>Check-in from 15:00</p>

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
    <p>Check-out at 11:00</p>
    <div>
        <span>Cost: </span><span>$ <span id="room-cost"></span></span>
    </div>

    <!-- Features information -->
    <h3>Activities</h3>

    <?php
    $groupedFeatures = [];
    foreach ($featuresInfo as $feature) :
        $category = $feature['category'];
        $groupedFeatures[$category][] = $feature;
    endforeach;
    ?>

    <?php foreach ($groupedFeatures as $category => $features) : ?>
        <h5 class="category-name"><?= $category === 'hotel-specific' ? 'spa' : $category; ?></h5>

        <?php foreach ($features as $feature) : ?>
            <input
                type="checkbox"
                name="feature_ids[]"
                id="feature_<?= $feature['id']; ?>"
                value="<?= $feature['id']; ?>">

            <label for="feature_<?= $feature['id']; ?>">
                <span>
                    <?php echo $feature['name']; ?>
                </span>
                <span>
                    $<?= $feature['price']; ?>
                </span>
            </label>
            <br>
        <?php endforeach; ?>
    <?php endforeach; ?>

    <div>
        <span>Cost: </span><span>$ <span id="room-cost"></span></span>
    </div>

    <!-- Information about the guest -->

    <h3>Guest information </h3>
    <label for="name">
        Enter your name:
    </label>
    <input type="text"
        id="name"
        name="name"
        required><br>

    <label for="api_key">
        Enter your API-Key:
    </label>
    <input type="text"
        id="api_key"
        name="api_key"
        required><br>

    <div>
        <h4>Total price:</h4>
        <p id="price"></p>
    </div>

    <input
        type="hidden"
        id="room_id"
        name="room_id"
        value="<?php echo $room['id']; ?>">

    <button type="submit">Book</button>
</form>