<?php

require __DIR__ . '/../src/database/data.php';

// if (session_status() === PHP_SESSION_NONE) {
//     session_start();
// }

// $errors = $_SESSION['errors'] ?? null;

// unset($_SESSION['errors']);

$roomId = $_GET['id'];
$query = $database->prepare('SELECT * FROM rooms WHERE id = :roomId');
$query->execute([':roomId' => $roomId,]);
$room = $query->fetch(PDO::FETCH_ASSOC);

$query = $database->query('SELECT * FROM room_amenity JOIN amenities ON room_amenity.amenity_id = amenities.id');
$amenities = $query->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<?php require __DIR__ . '/metadata/head.php'; ?>

<body>
    <img class="sub-bg" src="../assets/images/terracotta-hotel.png">
    <main>
        <section class="booking-container">
            <aside class="calendar-container">
                <h1>
                    Availability
                </h1>
                <?php require __DIR__ . '/components/calendar.php'; ?>
                <div class="calendar-tip">
                    <div class="tip-items">
                        <div class="tip-item booked"></div>
                        Fully booked
                    </div>
                    <div class="tip-items">
                        <div class="tip-item available"></div>
                        Room available
                    </div>
                </div>
            </aside>
            <article class="room-container">
                <header class="room-header">
                    <figure>
                        <img class="room-img" src="../assets/images/<?php echo $room['room_image']; ?>">
                    </figure>
                    <h1>
                        <?php echo $room['tier'] ?> room
                    </h1>
                    <span>
                        Cost per night: $<span id="price-per-night" data-price="<?php echo $room['price_per_night']; ?>"><?php echo $room['price_per_night']; ?></span>
                    </span>
                </header>
                <div class="room-body">
                    <div class="room-desc">
                        <p>
                            <?php echo $room['description']; ?>
                        </p>
                        <?php require __DIR__ . '/components/amenities.php'; ?>
                    </div>

                    <?php if (isset($_GET['errors'])) {
                        $errors = $_GET['errors'];
                        $errors = explode('£', $errors); ?>
                        <div id="error_msgs">
                            <?php foreach ($errors as $error) : ?>
                                <div class="msg-card error-s">
                                    <p>
                                        <span class="material-symbols-outlined">
                                            error
                                        </span><?php echo $error; ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php } ?>

                    <?php require __DIR__ . '/components/booking-form.php'; ?>
                </div>
            </article>
        </section>
    </main>


    <?php require __DIR__ . '/components/footer.php'; ?>