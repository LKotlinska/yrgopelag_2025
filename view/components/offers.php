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

                    <img class="event-img" src="./assets/images/<?php echo $offer['image']; ?>">

                    <div class="event-content">
                        <div>
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
                            <?php foreach ($rooms as $room) :
                                if ($room['id'] === $offer['included_room']) { ?>
                                    <p class="include-item">
                                        <span class="material-symbols-outlined">
                                            check
                                        </span>
                                        Applies to <?php echo $room['tier']; ?> room
                                    </p>
                            <?php }
                            endforeach; ?>
                            <p class="include-item">
                                <span class="material-symbols-outlined">
                                    check
                                </span>
                                Discount - $<?php echo $offer['discount_value']; ?>
                            </p>
                        </div>
                        <div class="btn-container event-btn-container">

                            <a class="hbtn hb-fill-middle-rev-bg a-link" href="./view/booking.php?room_id=<?php echo $offer['included_room']; ?>&offer_id=<?php echo $offer['id']; ?>">
                                Explore this package</span>
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach ?>
        </div>
    </section>

<?php } ?>