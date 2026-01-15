<?php

require __DIR__ . '/../../src/database/data.php';
$query = $database->query('SELECT * FROM rooms');
$rooms = $query->fetchAll(PDO::FETCH_ASSOC);

?>

<section id="main-r-section">
    <h2>Explore our rooms</h2>
    <div class="r-container">
        <?php foreach ($rooms as $room) : ?>
            <a class="r-item" href="./view/booking.php?room_id=<?php echo $room['id']; ?>">
                <div class="r-item-header">
                    <h3><?php echo $room['tier']; ?></h3>
                    <p> From $<?php echo $room['price_per_night'] ?></p>
                </div>
                <img
                    class="anchor-img"
                    src="./assets/images/rooms/<?php echo $room['preview_image']; ?>"
                    alt="Image of <?php echo $room['tier']; ?> room">
            </a>
        <?php endforeach; ?>
    </div>
</section>