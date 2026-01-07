<?php

session_set_cookie_params([
    // Session unsets when browser closes
    'lifetime' => 0,
    'path' => '/',
    'httponly' => true,
    'secure' => isset($_SERVER['HTTPS']),
    'samesite' => 'Strict',
]);

session_start();
