<?php require __DIR__ . '/../../src/database/data.php';

$query = $database->query('SELECT stars FROM hotel_info');
$stars = (int) $query->fetchColumn();

?>
<header class="main-h">

    <img class="header-overlay" src="./assets/images/terracotta-hotel.png" alt="" />

    <?php require __DIR__ . '/navigation.php'; ?>
    <div class="header-container">
        <hgroup class="site-logo">
            <?php require __DIR__ . '/logo.php'; ?>
        </hgroup>
        <div class="star-container">
            <?php for ($i = 0; $i < $stars; $i++) : ?>
                <span class="material-symbols-outlined">
                    star
                </span>
            <?php endfor; ?>
        </div>
        <article class="intro-card">
            <p>Nestled by the shoreline, where warm stone meets soft sand and the sea sets the rhythm, our spa hotel is designed for rest, renewal, and being present, without the noise.</p>
            <span class="slogan">
                A place to arrive - and let go
            </span>
        </article>
    </div>
</header>