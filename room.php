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

<!DOCTYPE html>
<html lang="en">

<?php require __DIR__ . '/view/head.php'; ?>

<body class="r-page">
    <main>
        <section class="room-container">
            <aside class="calendar">
                <h1>
                    Room availability
                </h1>
                <?php require __DIR__ . '/view/calendar.php'; ?>
            </aside>
            <article class="room-card">
                <header class="room-header">
                    <figure>
                        <img class="room-img" src="./assets/images/<?php echo $room['room_image']; ?>">
                    </figure>
                    <h1>
                        <?php echo $room['tier'] ?> room
                    </h1>
                    <span>
                        Cost per night: $<?php echo $room['price_per_night']; ?>
                    </span>
                </header>
                <div class="room-body">
                    <div class="room-desc">
                        <p>
                            <?php echo $room['description']; ?>
                        </p>
                        <?php require __DIR__ . '/view/amenities.php'; ?>
                    </div>
                    <?php require __DIR__ . '/view/booking-form.php'; ?>
                </div>
            </article>
        </section>
    </main>


    <?php require __DIR__ . '/view/footer.php'; ?>