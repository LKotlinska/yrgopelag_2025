<?php

require __DIR__ . '/../src/database/data.php';

// Prevent refreshing
unset($_SESSION['booking_id']);

session_start();


if (!isset($_SESSION['booking_id'])) {
    header("Location: ../index.php");
    exit;
}

$bookingId = $_SESSION['booking_id'];

$query = $database->prepare(
    'SELECT guests.name, rooms.tier, booking_receipt.arrival_date, booking_receipt.departure_date, booking_receipt.amount_paid
    FROM booking_receipt 
    JOIN rooms 
    ON rooms.id = booking_receipt.room_id
    JOIN guests
    ON guests.id = booking_receipt.guest_id
    WHERE booking_receipt.id = :id'
);

$query->execute([
    ':id' => $bookingId
]);

$booking = $query->fetch(PDO::FETCH_ASSOC);

$query = $database->prepare(
    'SELECT features.name 
    FROM features 
    JOIN feature_bookings 
    ON feature_bookings.feature_id = features.id 
    WHERE feature_bookings.booking_id = :bookingId'
);
$bookingId = 9;
$query->execute([
    ':bookingId' => $bookingId
]);

$features = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<?php require __DIR__ . '/metadata/head.php'; ?>

<body>

    <img class="sub-bg" src="../assets/images/terracotta-hotel.png">
    <main>
        <div class="msg-card booking-c">
            <p>
                <span class="material-symbols-outlined">
                    error
                </span>
                Booking successful!
            </p>
        </div>
        <section>
            <article class="receipt-container">
                <h1>Booking confirmation</h1>
                <p>Thank you for your reservation, <?php echo $booking['name'] ?>.</p>
                <p>Your stay has been successfully booked, and we’re looking forward to welcoming you.</p>

                <h2>Your stay</h2>
                <table class="receipt-table">
                    <tbody>
                        <tr>
                            <th class="f-weight">
                                Check-in:
                            </th>
                            <td>
                                <?php echo $booking['arrival_date'] ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="f-weight">
                                Check-out:
                            </th>
                            <td>
                                <?php echo $booking['departure_date']; ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="f-weight">
                                Room:
                            </th>
                            <td>
                                <?php echo $booking['tier']; ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="f-weight">
                                Features:
                            </th>
                            <td>
                                <?php foreach ($features as $index => $feature): ?>
                                    <li class="receipt-li"><?php echo $feature['name']; ?></li>
                                <?php endforeach; ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="f-weight">
                                Total price:
                            </th>
                            <td>
                                $<?php echo $booking['amount_paid']; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p>A confirmation has been saved with your booking details.</p>
                <p>We’ll take care of the rest.</p>
                <p>All that’s left for you is to arrive — and let go.</p>

                <nav class="nav-list">
                    <a class="btn action-btn" href="../../index.php">Return to main page</a>
                </nav>

            </article>
        </section>
    </main>
</body>

<?php require __DIR__ . '/components/footer.php'; ?>