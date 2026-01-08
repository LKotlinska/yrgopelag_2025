<?php
require __DIR__ . '/../src/config/admin.config.php';
require __DIR__ . '/../src/controllers/admin.php';

if (!isset($_SESSION['admin'])) {
    $_SESSION['errors'] = [
        'STOP! You violated the law. Pay the court a fine or serve your sentence.'
    ];
    header('Location: login.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<?php require __DIR__ . '/metadata/head.php'; ?>

<body>
    <main>
        <a href="../src/controllers/logout.php">Logout</a>
        <?php if (!empty($errors)) {
            require __DIR__ . '/components/form/messages.php';
        } ?>

        <h1>Room prices</h1>
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
            <h1>Update prices for active features</h1>
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
                    <?php foreach ($features as $feature) : ?>
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
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <h1>Active Offers</h1>
        <table>
            <thead>

                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Includes desc</th>
                    <th>Includes room</th>
                    <th>Image</th>
                    <th>Discount Type</th>
                    <th>Discount Value</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($offers as $offer) : ?>
                    <tr>
                        <td>
                            <?php echo $offer['id'] ?>
                        </td>
                        <td>
                            <?php echo $offer['name'] ?>
                        </td>
                        <td>
                            <?php echo $offer['description'] ?>
                        </td>
                        <td>
                            <?php echo $offer['included_desc'] ?>
                        </td>
                        <td>
                            <?php echo $offer['included_room'] ?>
                        </td>
                        <td>
                            <?php echo $offer['image'] ?>
                        </td>
                        <td>
                            <?php echo $offer['discount_type'] ?>
                        </td>
                        <td>
                            <?php echo $offer['discount_value'] ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h1>Edit OR Add Offers</h1>
        <table>
            <thead>
                <tr>
                    <th>Action type</th>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Includes desc</th>
                    <th>Includes room</th>
                    <th>Image</th>
                    <th>Discount Type</th>
                    <th>Discount Value</th>
                    <th>Is Active</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <form method="POST">
                    <tr>
                        <td>
                            <select
                                name="offer_action"
                                required>
                                <option value="edit">
                                    Edit
                                </option>
                                <option value="add">
                                    Add
                                </option>
                            </select>
                        </td>
                        <td>
                            <input
                                type="number"
                                name="offer_id"
                                required />
                        </td>
                        <td>
                            <input
                                type="text"
                                name="offer_name"
                                required />
                        </td>
                        <td>
                            <input
                                type="text"
                                name="offer_desc"
                                required />
                        </td>
                        <td>
                            <input
                                type="text"
                                name="offer_incl_desc"
                                required />
                        </td>
                        <td>
                            <input
                                type="number"
                                name="offer_incl_room"
                                required />
                        </td>
                        <td>
                            <input
                                type="text"
                                name="offer_img"
                                required />
                        </td>
                        <td>
                            <input
                                type="text"
                                name="offer_disc_type"
                                required />
                        </td>
                        <td>
                            <input
                                type="number"
                                name="offer_disc_value"
                                required />
                        </td>
                        <td>
                            <select
                                name="offer_is"
                                required>
                                <option value="1">
                                    True
                                </option>
                                <option value="0">
                                    False
                                </option>
                            </select>
                        </td>
                        <td>
                            <button type="submit">Save</button>
                        </td>
                    </tr>
                </form>
            </tbody>
        </table>

        <h1>Active offer features</h1>
        <table>
            <thead>
                <tr>
                    <th>Offer ID</th>
                    <th>Offer</th>
                    <th>Feature ID</th>
                    <th>Feature</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($offerFeature as $group) : ?>
                    <tr>
                        <td>
                            <?php echo $group['o_id']; ?>
                        </td>
                        <td>
                            <?php echo $group['o_name']; ?>
                        </td>
                        <td>
                            <?php echo $group['f_id']; ?>
                        </td>
                        <td>
                            <?php echo $group['f_name']; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <h1>Add or remove features to offer</h1>
        <table>
            <thead>
                <tr>
                    <th>Action type</th>
                    <th>Feature</th>
                    <th>Offer Id</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <form method="POST">
                        <td>
                            <select
                                name="feature_action"
                                required>
                                <option value="add">
                                    Add
                                </option>
                                <option value="remove">
                                    Remove
                                </option>
                            </select>
                        </td>
                        <td>
                            <select name="o_feature_id">
                                <?php foreach ($features as $feature) : ?>
                                    <option value="<?php echo $feature['id']; ?>">
                                        <?php echo $feature['tier'] . ' - ' . $feature['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>
                            <select name="f_offer_id">
                                <?php foreach ($offers as $offer) : ?>
                                    <option value="<?php echo $offer['id']; ?>">
                                        <?php echo $offer['id'] . ' - ' . $offer['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                        </td>
                        <td>
                            <button type="submit">Add</button>
                        </td>
                    </form>
                </tr>
            </tbody>
        </table>
    </main>
</body>

</html>