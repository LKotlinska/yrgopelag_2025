<?php

session_start();

require __DIR__ . '/../src/database/data.php';

require __DIR__ . '/../src/controllers/offers.php';

// Handle errors after booking fails
$errors = $_SESSION['errors'] ?? [];

// Fill form with old data
$old = $_SESSION['old'] ?? [];

unset($_SESSION['errors']);
unset($_SESSION['errors'], $_SESSION['old']);

// Room id to display content -> calendar + room info
$roomId = (int) $_GET['room_id'];

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
        <a class="hbtn hb-fill-middle-rev-bg" href="../index.php">Return to main page</a>
        <section class="booking-section">
            <aside class="aside-container">
                <div>
                    <?php require __DIR__ . '/components/calendar.php'; ?>
                </div>
                <div>
                    <h2>Discount</h2>
                    <p>Returning guests receive a $2 discount!</p>
                    <p>Applies when booked using the same name.</p>
                </div>
            </aside>

            <!-- Room information -->
            <article class="booking-container">
                <header class="booking-header">
                    <figure class="image-stack">
                        <img class="booking-img" src="../assets/images/<?php echo $room['room_image']; ?>">
                        <img class="booking-img" src="../assets/images/<?php echo $room['bathroom_image']; ?>">
                    </figure>
                    <h2>
                        <?php echo $room['tier'] ?> room
                    </h2>
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
                    <?php require __DIR__ . '/components/form/messages.php'; ?>

                    <!-- Display booking form -->
                    <?php require __DIR__ . '/components/booking-form.php'; ?>
                    <div class="form-tip">
                        <span class="field-req">*</span>
                        Field is required
                    </div>
                </div>
            </article>
        </section>
    </main>


    <?php require __DIR__ . '/components/footer.php'; ?>