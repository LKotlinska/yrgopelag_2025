<p>Room includes:</p>
<div>
    <?php
    $count = 0;
    foreach ($amenities as $amenity) :
        if ($amenity['room_id'] === $room['id']) :
            if ($count === 3) {
                break;
            } ?>
            <p class="include-item"><span class="material-symbols-outlined">
                    check_box
                </span> <?php echo $amenity['name']; ?></p>
    <?php $count++;
        endif;
    endforeach;
    ?>
</div>