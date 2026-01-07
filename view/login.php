<?php

require __DIR__ . '/../src/config/admin.config.php';

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

?>

<!DOCTYPE html>
<html lang="en">
<?php require __DIR__ . '/metadata/head.php'; ?>

<body>

    <?php if (!empty($errors)) {
        require __DIR__ . '/components/form/messages.php';
    } ?>

    <h2>Log in</h2>
    <form method="POST" action="../src/backend/login.admin.php">

        <label for="username">
            Username
        </label>
        <input
            type="text"
            id="username"
            name="username"
            required />

        <label for="password">
            Password
        </label>
        <input
            type="password"
            id="password"
            name="password"
            required />
        <button type="submit">
            Login
        </button>

    </form>

</body>

</html>