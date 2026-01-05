<p>Room includes:</p>
<div>
    <?php
    foreach ($amenities as $amenity) :
        if ($amenity['room_id'] === $room['id']) { ?>
            <p class="include-item"><span class="material-symbols-outlined">
                    check
                </span> <?php echo $amenity['name']; ?></p>
    <?php }
    endforeach; ?>
</div>