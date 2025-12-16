<form method="POST" action="">
    <label for="name">Enter your name:</label>
    <input type="name" id="name" name="name"><br>

    <label for="room-id">Select room</label>
    <select name="room-id" id="room-id">
        <?php foreach ($rooms as $room) : ?>
            <option value="<?php $room['id']; ?>">
                <?php echo $room['tier']; ?>
            </option>
        <?php endforeach; ?>
    </select><br>

    <label for="arrival-date">Arrival Date:</label>
    <input type="date" id="arrival-date" name="arrival-date"><br>

    <label for="departure-date">Departure Date:</label>
    <input type="date" id="departure-date" name="departure-date"><br>

    <label for="transfer-code">Enter your transferCode:</label>
    <input type="transfer-code" id="transfer-code" name="transfer-code"><br>

    <button type="submit">Book</button>
</form>
<?php print_r($rooms); ?>