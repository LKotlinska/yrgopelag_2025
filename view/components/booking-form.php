<?php
require __DIR__ . '/../../src/database/data.php';

$query = $database->query('SELECT * FROM features');
$featuresInfo = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT * FROM rooms');
$rooms = $query->fetchAll(PDO::FETCH_ASSOC);

?>
<form method="POST" id="booking-form" action="../src/backend/bookings.php">
    <h2>Dates</h2>
    <div class="form-section">
        <div class="date-select">
            <div class="date-item">
                <label for="arrival_date">
                    Arrival
                </label>
                <input
                    type="date"
                    id="arrival_date"
                    name="arrival_date"
                    min="2026-01-01"
                    max="2026-01-31"
                    required>
                <span>Check-in from 15:00</span>
            </div>
            <div class="date-item">
                <label for="departure_date">
                    Departure
                </label>
                <input
                    type="date"
                    id="departure_date"
                    name="departure_date"
                    min="2026-01-01"
                    max="2026-01-31"
                    required>
                <span>Check-out at 11:00</span>
            </div>
        </div>
        <div class="cost-display">
            <span>Cost: </span><span>$ <span id="room-cost"></span></span>
        </div>
    </div>

    <!-- Features information -->
    <h2>Activities</h2>
    <div class="form-section">
        <?php
        $groupedFeatures = [];
        foreach ($featuresInfo as $feature) :
            $category = $feature['category'];
            $groupedFeatures[$category][] = $feature;
        endforeach;
        ?>
        <div class="feature-container">
            <?php foreach ($groupedFeatures as $category => $features) : ?>
                <div class="feature-card">
                    <span class="category-name"><?php echo $category === 'hotel-specific' ? 'spa' : $category; ?></span>
                    <?php foreach ($features as $feature) : ?>
                        <div class="feature-items">
                            <input
                                type="checkbox"
                                name="feature_ids[]"
                                id="feature_<?php echo $feature['id']; ?>"
                                value="<?php echo $feature['id']; ?>"
                                data-price="<?php echo $feature['price']; ?>">
                            <label for="feature_<?php echo $feature['id']; ?>">
                                <span class="feature-name">
                                    <?php echo $feature['name']; ?>
                                </span>
                                <span>
                                    $
                                    <span class="feature-price">
                                        <?php echo $feature['price']; ?>
                                    </span>
                                </span>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>

        </div>

        <div class="cost-display">
            <span>Cost: </span><span>$ <span id="feature-cost"></span></span>
        </div>
    </div>

    <!-- Guest information field -->
    <h2>Guest information </h2>
    <div class="form-section">
        <div class="guest-info">
            <label for="name">
                Enter your name:
            </label>
            <input type="text"
                id="name"
                name="name"
                placeholder="e.g. Rune"
                required>
        </div>
    </div>

    <!-- Payment fields -->
    <h2>Payment</h2>
    <div class="form-section">
        <div class="guest-info">
            <label>
                <input type="radio" name="payment_method" value="transfer_code">
                Pay with TransferCode
            </label>
            <label>
                <input type="radio" name="payment_method" value="api_key" checked>
                Pay with API-Key
            </label>
        </div>

        <!-- Input fields for payment -->
        <div class="guest-info" id="api-key-field">
            <label for="api_key">
                Enter your API-Key:
            </label>
            <input type="text"
                id="api_key"
                name="api_key"
                placeholder="uuid-string">
        </div>

        <div class="guest-info" id="transfer-code-field" style="display:none;">
            <label for="transfer_code">Enter your TransferCode:</label>
            <input
                type="text"
                id="transfer_code"
                name="transfer_code"
                placeholder="uuid-transfer-code">
        </div>
    </div>
    <!-- GUEST INFO -->



    <!-- Information about the guest -->
    <!--
        <div class="guest-info">
            <label for="api_key">
                Enter your API-Key:
            </label>
            <input type="text"
                id="api_key"
                name="api_key"
                placeholder="uuid-string"
                required>
        </div>
    </div> -->

    <div class="cost-display">
        <span>Total: </span><span>$ <span id="total-cost"></span></span>
    </div>

    <input
        type="hidden"
        id="room_id"
        name="room_id"
        value="<?php echo $room['id']; ?>">

    <button class="btn action-btn" type="submit">Book</button>
</form>