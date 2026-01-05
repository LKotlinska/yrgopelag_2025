<?php require __DIR__ . '/../src/backend/admin.php'; ?>

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
                            name="room_price"
                            required>
                        <input
                            type="hidden"
                            name="room_id"
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

<div>
    <table>
        <thead>
            <th>Id</th>
            <th>Name</th>
            <th>Category</th>
            <th>Tier</th>
            <th>Current price</th>
            <th>New price</th>
            <th>Action</th>
        </thead>
        <tbody>
            <?php foreach ($features as $feature) :
                if ($feature['is_active']) { ?>
                    <tr>
                        <form method="POST">
                            <td>
                                <?php echo $feature['id']; ?>
                            </td>
                            <td>
                                <?php echo $feature['name']; ?>
                            </td>
                            <td>
                                <?php echo $feature['category']; ?>
                            </td>
                            <td>
                                <?php echo $feature['tier']; ?>
                            </td>
                            <td>
                                <?php echo $feature['price']; ?>
                            </td>
                            <td>
                                <input
                                    type="number"
                                    name="feature_price"
                                    required>
                                <input
                                    type="hidden"
                                    name="feature_id"
                                    value="<?php echo $feature['id']; ?>">
                            </td>
                            <td>
                                <button type="submit">Save</button>
                            </td>
                        </form>
                    </tr>
            <?php }
            endforeach; ?>
        </tbody>
    </table>
</div>