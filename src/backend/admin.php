<?php
$query = $database->query('SELECT * FROM rooms');
$rooms = $query->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['id'], $_POST['price'])) {
    $roomId = $_POST['id'];
    $roomPrice = $_POST['price'];

    $updateRooms = $database->prepare('UPDATE rooms SET price_per_night = :price WHERE id = :id');
    $updateRooms->bindParam(':id', $roomId);
    $updateRooms->bindParam(':price', $roomPrice);
    $updateRooms->execute();
    echo $roomId . $roomPrice;
}
