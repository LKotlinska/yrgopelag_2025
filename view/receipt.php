<?php

require __DIR__ . '/../src/database/data.php';

require __DIR__ . '/../src/functions/guest.functions.php';

// Prevent refreshing
unset($_SESSION['booking_id']);

session_start();


if (!isset($_SESSION['booking_id'])) {
    header("Location: ../index.php");
    exit;
}

$bookingId = $_SESSION['booking_id'];

$query = $database->prepare(
    'SELECT guests.name, rooms.tier, booking_receipts.arrival_date, booking_receipts.departure_date, booking_receipts.amount_paid
    FROM booking_receipts 
    JOIN rooms 
    ON rooms.id = booking_receipts.room_id
    JOIN guests
    ON guests.id = booking_receipts.guest_id
    WHERE booking_receipts.id = :id'
);

$query->execute([
    ':id' => $bookingId
]);

$booking = $query->fetch(PDO::FETCH_ASSOC);
$guestName = $booking['name'];

$query = $database->prepare(
    'SELECT features.name 
    FROM features 
    JOIN booking_features
    ON booking_features.feature_id = features.id 
    WHERE booking_features.booking_id = :bookingId'
);

$query->execute([
    ':bookingId' => $bookingId
]);
$features = $query->fetchAll(PDO::FETCH_ASSOC);

$isReturnCustomer = isExistingGuest($database, $guestName);

?>

<?php require __DIR__ . '/metadata/head.booking.php'; ?>

<body>

    <img
        class="sub-bg"
        src="../assets/images/terracotta-hotel.png"
        alt="Background image of Terracotta Bay Spa & Hotel">
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
                    </tbody>
                </table>
                <table class="receipt-table price-table">
                    <tbody>
                        <?php if ($isReturnCustomer) { ?>
                            <tr>
                                <th class="f-weight">
                                    Loyalty discount:
                                </th>
                                <td>
                                    - $2
                                </td>
                            </tr>
                        <?php } ?>
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
                    <a class="hbtn hb-fill-middle-rev-bg a-link" href="../index.php">Return to main page</a>
                </nav>

            </article>
        </section>
    </main>
</body>

<?php require __DIR__ . '/components/footer.php'; ?>