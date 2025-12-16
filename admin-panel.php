<?php require __DIR__ . '/src/backend/admin.php'; ?>

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