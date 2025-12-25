<?php

require __DIR__ . '/../src/database/data.php';
$query = $database->query('SELECT * FROM rooms');
$rooms = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT * FROM room_amenity JOIN amenities ON room_amenity.amenity_id = amenities.id');
$amenities = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<main>

    <section>
        <article class="intro-card">
            <p>At Terracotta Bay, time moves a little slower.</p>
            <p>Nestled by the shoreline, where warm stone meets soft sand and the sea sets the rhythm, our spa hotel is designed for rest, renewal, and quiet moments of presence.</p>
            <p>Everything is shaped to help you feel at ease.</p>
        </article>
    </section>

    <section class="anchor-section">
        <div class="anchor-container">
            <div class="anchor-item">
                <a href="#">
                    <h3>Spa</h3>
                    <img class="anchor-img" src="/assets/images/spa.png" alt="" />
                </a>
            </div>
            <div class="anchor-item">
                <a href="#">
                    <h3>Rooms</h3>
                    <img class="anchor-img" src="/assets/images/rooms.png" alt="" />
                </a>
            </div>
            <div class="anchor-item">
                <a href="#">
                    <h3>Offers</h3>
                    <img class="anchor-img" src="/assets/images/offers.png" alt="" />
                </a>
            </div>
        </div>
    </section>

    <section>
        <?php foreach ($rooms as $room) : ?>
            <article class="room-card">
                <figure>
                    <img
                        class="room-image"
                        src="/assets/images/<?php echo $room['room_image']; ?>"
                        alt="Room image">
                </figure>

                <div class="room-info">
                    <h3><?php echo $room['tier']; ?> room</h3>
                    <p><?php echo $room['description']; ?></p>
                    <p>Room includes:</p>
                    <div>
                        <?php
                        $count = 0;
                        foreach ($amenities as $amenity) :
                            if ($amenity['room_id'] === $room['id']) :
                                if ($count === 3) {
                                    break;
                                } ?>
                                <p class="include-item"><span class="material-symbols-outlined">
                                        check_box
                                    </span> <?php echo $amenity['name']; ?></p>
                        <?php $count++;
                            endif;
                        endforeach;
                        ?>

                    </div>

                    <button class="btn">View details</button>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
</main>