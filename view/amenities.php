<p>Room includes:</p>
<div>
    <?php
    $count = 0;
    foreach ($amenities as $amenity) :
        if ($amenity['room_id'] === $room['id']) { ?>
            <p class="include-item"><span class="material-symbols-outlined">
                    check
                </span> <?php echo $amenity['name']; ?></p>
    <?php $count++;
            if (!isset($_GET['id']) && $count === 3) {
                break;
            }
        }
    endforeach; ?>
</div>