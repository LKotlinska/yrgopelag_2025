<?php

declare(strict_types=1);

require __DIR__ . '/../database/data.php';

require __DIR__ . '/../functions/admin.functions.php';

require __DIR__ . '/../config/admin.config.php';


if (isset($_POST['username'], $_POST['password'])) {
    $username = filter_var(trim($_POST['username']), FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = trim($_POST['password']);

    $adminUsername = $_ENV['USERNAME'];
    $adminPassword = $_ENV['PASSWORD'];

    $result = handleLogin($username, $password, $adminUsername, $adminPassword);

    if (!empty($result['success'])) {
        session_regenerate_id(true);
        $_SESSION['admin'] = $username;
        header('Location: ../../view/admin.panel.php');
        exit;
    }

    $errors = $result['errors'] ?? ['Invalid username or password'];

    if (!is_array($errors)) {
        $errors = [$errors];
    }

    $_SESSION['errors'] = $errors;
    header('Location: ../../view/login.php');
    exit;
}
