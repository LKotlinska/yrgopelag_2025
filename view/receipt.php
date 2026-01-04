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
    'SELECT rooms.tier, room_bookings.arrival_date, room_bookings.departure_date, room_bookings.amount_paid
    FROM room_bookings 
    JOIN rooms 
    ON rooms.id = room_bookings.room_id 
    WHERE room_bookings.id = :id'
);

$query->execute([
    ':id' => $bookingId
]);

$booking = $query->fetch(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="en">

<?php require __DIR__ . '/head.php'; ?>

<body class="r-page">
    <main>
        <section>
            <article class="receipt-container">
                <h1>Booking confirmation</h1>
                <p>Thank you for your reservation at Terracotta Bay Spa & Hotel.</p>
                <p>Your stay has been successfully booked, and we’re looking forward to welcoming you.</p>

                <h2>Your stay</h2>
                <p>
                    <span>Check-in: </span><?php echo $booking['arrival_date'] ?> from 15:00
                </p>
                <p>
                    <span>Check-out: </span><?php echo $booking['departure_date']; ?> at 11:00
                </p>
                <p>
                    <span>Room: </span><?php echo $booking['tier'] ?>
                </p>
                <p>
                    <span>Total price: $</span><?php echo $booking['amount_paid'] ?>
                </p>
                <p>A confirmation has been saved with your booking details.</p>
                <p>We’ll take care of the rest.</p>
                <p>All that’s left for you is to arrive — and let go.</p>
            </article>
        </section>
    </main>
</body>

</html>