<?php
require __DIR__ . '/../config/admin.config.php';

session_unset();
session_destroy();

header('Location: ../../view/login.php');
exit;
