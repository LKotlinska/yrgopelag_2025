<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="stylesheet" href="./../src/styles/style.css">
    <script src="./../src/scripts/main.js"></script>
    <title>Terracota Bay Hotel</title>
</head>

<body>
    <header class="main-h">
        <?php
        // require __DIR__ . '/navigation.php' 
        ?>
        <h1 class="menu">MENU</h1>
        <h1 class="book">BOOK</h1>
        <img class="header-overlay" src="assets/images/terracotta-hotel.png" alt="" />
        <hgroup class="site-logo">
            <?php require __DIR__ . '/logo.php'; ?>
            <span class="slogan">
                A place to arrive - and let go
            </span>
        </hgroup>
    </header>