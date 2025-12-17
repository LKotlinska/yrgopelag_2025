<form method="POST" id="booking-form" action="../src/backend/bookings.php">
    <label for="name">Enter your name:</label>
    <input type="text" id="name" name="name" required><br>

    <label for="room-id">Select room</label>
    <select name="room-id" id="room-id" required>
        <?php foreach ($rooms as $room) : ?>
            <option
                value="<?= $room['id']; ?>"
                data-price="<?= $room['price_per_night']; ?>">
                <?= $room['tier'] . ' $' . $room['price_per_night']; ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <label for="arrival-date">Arrival date:</label>
    <input type="date" id="arrival-date" name="arrival-date" min="2026-01-01" max="2026-01-31" required><br>

    <label for="departure-date">Departure date:</label>
    <input type="date" id="departure-date" name="departure-date" required><br>

    <label for="transfer-code">Enter your transferCode:</label>
    <input type="text" id="transfer-code" name="transfer-code" required><br>

    <div>
        <h4>Total price:</h4>
        <p id="price"></p>
        <input type="hidden" name="total-price" id="total-price">
    </div>


    <button type="submit">Book</button>
</form>