<form method="POST" id="booking-form">
    <label for="name">Enter your name:</label>
    <input type="text" id="name" name="name" required><br>

    <label for="room-id">Select room</label>
    <select name="room-id" id="room-id" required>
        <?php foreach ($rooms as $room) : ?>
            <option value="<?php echo $room['price_per_night']; ?>">
                <?php echo $room['tier'] . ' $' . $room['price_per_night']; ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <label for="arrival-date">Arrival date:</label>
    <input type="date" id="arrival-date" name="arrival-date" required><br>

    <label for="departure-date">Departure date:</label>
    <input type="date" id="departure-date" name="departure-date" required><br>

    <label for="transfer-code">Enter your transferCode:</label>
    <input type="text" id="transfer-code" name="transfer-code" required><br>

    <p>Price: </p>

    <p id="price"></p>

    <button type="submit">Book</button>
</form>

<?php print_r($rooms); ?>