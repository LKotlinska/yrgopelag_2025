<?php

require __DIR__ . '/../../src/database/data.php';
$query = $database->query('SELECT * FROM rooms');
$rooms = $query->fetchAll(PDO::FETCH_ASSOC);

$query = $database->query('SELECT * FROM room_amenity JOIN amenities ON room_amenity.amenity_id = amenities.id');
$amenities = $query->fetchAll(PDO::FETCH_ASSOC);
?>

<main>
    <?php require __DIR__ . '/rooms.php' ?>
    <section id="event-section">
        <article class="event-card">
            <div>
                <h2>The spa package</h2>
                <p>A quiet refuge designed for rest.</p>
                <p>Our spa is open to warm air and distant waves. Soft light, flowing water, and natural textures create a setting that feels private, calm, and quietly indulgent.</p>
                <p class="include-item">
                    <span class="material-symbols-outlined">
                        check
                    </span>
                    All hotel-specific features
                </p>
                <a class="a-link" href="#">
                    Explore this package</span>
                </a>
            </div>
            <div class="event-item">
                <img class="event-img" src="./assets/images/spa.png">
            </div>
        </article>
        <article class="event-card">
            <div class="event-item">
                <img class="event-img" src="./assets/images/spa.png">
            </div>
            <div>
                <h2>By the water</h2>
                <p>Quiet swims and playful immersion.</p>
                <p>Our guests have access to a variety of water experiences. Serene pools for slow mornings to more lively options for those seeking activity.</p>
                <p>Designed to be enjoyed at your own pace, from sunrise to evening light.</p>
            </div>
        </article>
        <article class="event-card">
            <div>

                <h2>Leisure</h2>
                <p>Relaxed entertainment, thoughtfully curated.</p>
                <p>Our games selection is meant to be enjoyed without formality - a casual round, a shared laugh, or a quiet moment indoors. From classic table games to modern consoles, these spaces invite guests to unwind in a lighter, more playful way.</p>
                <p>Nothing competitive. Nothing rushed. Just time well spent.</p>
            </div>
            <div class="event-item">
                <img class="event-img" src="./assets/images/spa.png">
            </div>
        </article>
        <article class="event-card">
            <div class="event-item">
                <img class="event-img" src="./assets/images/spa.png">
            </div>
            <div>

                <h2>Ways to Wander</h2>
                <p>Explore beyond the bay.</p>
                <p>For guests who wish to move through the surroundings rather than simply observe them, a range of wheeled options is available. Choose a slow ride nearby or something with a bit more momentum, these experiences offer a different way to connect with the landscape.</p>
                <p>Freedom, with no fixed route.</p>
            </div>
        </article>
    </section>

</main>