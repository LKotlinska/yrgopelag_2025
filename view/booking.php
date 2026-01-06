<?php

session_start();

require __DIR__ . '/../src/database/data.php';

require __DIR__ . '/../src/backend/offers.php';

// Handle errors after booking fails
$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

// Room id to display content -> calendar + room info
$roomId = (int) $_GET['room_id'];

// Get offer_id if available

$query = $database->prepare('SELECT * FROM rooms WHERE id = :roomId');
$query->execute([':roomId' => $roomId,]);
$room = $query->fetch(PDO::FETCH_ASSOC);

$query = $database->query('SELECT * FROM room_amenity JOIN amenities ON room_amenity.amenity_id = amenities.id');
$amenities = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT * FROM features');
$featuresInfo = $query->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<?php require __DIR__ . '/metadata/head.php'; ?>

<script src="../src/scripts/booking.js"></script>

<body>
    <img class="sub-bg" src="../assets/images/terracotta-hotel.png">
    <main>
        <section class="booking-section">

            <?php require __DIR__ . '/components/calendar.php'; ?>
            <!-- Room information -->
            <article class="booking-container">
                <header class="booking-header">
                    <figure>
                        <img class="booking-img" src="../assets/images/<?php echo $room['room_image']; ?>">
                    </figure>
                    <h1>
                        <?php echo $room['tier'] ?> room
                    </h1>
                    <span>
                        Cost per night: $<span id="price-per-night" data-price="<?php echo $room['price_per_night']; ?>"><?php echo $room['price_per_night']; ?></span>
                    </span>
                </header>
                <div class="booking-body">
                    <div class="booking-desc">
                        <p>
                            <?php echo $room['description']; ?>
                        </p>
                        <?php require __DIR__ . '/components/amenities.php'; ?>
                    </div>

                    <!-- Display errors -->
                    <?php if (!empty($errors)) { ?>
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