<?php
require __DIR__ . '/../../src/database/data.php';

$query = $database->query('SELECT * FROM offers WHERE is_active 
= true');
$offers = $query->fetchAll(PDO::FETCH_ASSOC);

if (!empty($offers)) {
?>
    <section id="event-section">
        <h2>Explore our offers</h2>
        <div class="event-container">
            <?php foreach ($offers as $index => $offer) : ?>
                <article class="event-card">
                    <div>
                        <div class="event-content">
                            <h3>
                                <?php echo $offer['name']; ?>
                            </h3>
                            <p>
                                <?php echo $offer['description'] ?>
                            </p>
                            <p class="include-item">
                                <span class="material-symbols-outlined">
                                    check
                                </span>
                                <?php echo $offer['included_desc']; ?>
                            </p>
                        </div>
                        <a class="a-link" href="./view/booking.php?room_id=<?php echo $offer['included_room']; ?>&offer_id=<?php echo $offer['id']; ?>">
                            Explore this package</span>
                        </a>
                    </div>
                    <div class="event-item">
                        <img class="event-img" src="./assets/images/<?php echo $offer['image']; ?>">
                    </div>
                </article>
            <?php endforeach ?>
        </div>
    </section>

<?php } ?>