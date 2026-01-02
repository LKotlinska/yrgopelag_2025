<?php

require __DIR__ . '/../src/database/data.php';
$query = $database->query('SELECT * FROM rooms');
$rooms = $query->fetchAll(PDO::FETCH_ASSOC);

?>

<section class="main-r-section">
    <h2>Explore our rooms</h2>
    <div class="r-container">
        <?php foreach ($rooms as $room) : ?>
            <a class="r-item" href="./booking.php?id=<?php echo $room['id']; ?>">
                <h3><?php echo $room['tier']; ?></h3>
                <img
                    class="anchor-img"
                    src="./assets/images/<?php echo $room['preview_image']; ?>"
                    alt="Room image">
            </a>
        <?php endforeach; ?>
    </div>
</section>