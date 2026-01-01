<?php
require __DIR__ . '/src/database/data.php';

$roomId = $_GET['id'];
$query = $database->prepare('SELECT * FROM rooms WHERE id = :roomId');
$query->execute([':roomId' => $roomId,]);
$room = $query->fetch(PDO::FETCH_ASSOC);

$query = $database->query('SELECT * FROM room_amenity JOIN amenities ON room_amenity.amenity_id = amenities.id');
$amenities = $query->fetchAll(PDO::FETCH_ASSOC);

// echo '<pre>';
// print_r($room);
?>

<?php require __DIR__ . '/view/header.php'; ?>


<main>
    <aside class="calendar">
        <h2>
            Room availability
        </h2>
        <?php require __DIR__ . '/view/calendar.php'; ?>
    </aside>
    <article class="room-card">
        <header>
            <figure>
                <img class="room-img" src="/assets/images/<?php echo $room['room_image']; ?>">
            </figure>
            <h2>
                <?php echo $room['tier'] ?> room
            </h2>
            <p>
                Cost per night: $<?php echo $room['price_per_night']; ?>
            </p>
        </header>
        <section>
            <p>
                <?php echo $room['description']; ?>
            </p>
        </section>
        <section>
            <?php require __DIR__ . '/view/amenities.php'; ?>
        </section>
        <section>
            <?php require __DIR__ . '/view/booking-form.php'; ?>
        </section>
    </article>
</main>

<?php require __DIR__ . '/view/footer.php'; ?>