<?php
$database = new PDO('sqlite:' . __DIR__ . '/src/database/hotel.db');
$query = $database->query('SELECT * FROM rooms');
$rooms = $query->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['id'], $_POST['price'])) {
    $roomId = $_POST['id'];
    $roomPrice = $_POST['price'];

    $updateQuery = $database->prepare('UPDATE rooms SET price_per_night = :price WHERE id = :id');
    $updateQuery->bindParam(':id', $roomId);
    $updateQuery->bindParam(':price', $roomPrice);
    $updateQuery->execute();
    echo $roomId . $roomPrice;
}
?>

<table>
    <thead>
        <tr>
            <th>Id</th>
            <th>Room</th>
            <th>Current price</th>
            <th>New price</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rooms as $room) : ?>
            <tr>
                <form method="POST">
                    <td>
                        <?php echo $room['id']; ?>
                    </td>
                    <td>
                        <?php echo $room['tier'] ?>
                    </td>
                    <td>
                        <?php echo $room['price_per_night']; ?>
                    </td>
                    <td>
                        <input
                            type="number"
                            name="price"
                            required>
                        <input
                            type="hidden"
                            name="id"
                            value="<?php echo $room['id']; ?>">
                    </td>
                    <td>
                        <button type="submit">Save</button>
                    </td>
                </form>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>