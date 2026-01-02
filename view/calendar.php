<?php require __DIR__ . '/../src/backend/calendar.php';

$roomId = $_GET['id'];

?>
<table class="calendar">
    <thead>
        <tr>
            <th>Mon</th>
            <th>Tue</th>
            <th>Wed</th>
            <th>Thu</th>
            <th>Fri</th>
            <th>Sat</th>
            <th>Sun</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <?php
            $cellCount = 0;
            for ($cell = 1; $cell <= $cells; $cell++):
                // New row every 7 cells
                if ($cellCount === 7) {
            ?>
        </tr>
        <tr>
        <?php
                    $cellCount = 0;
                }
                if ($cell < $firstDayOfMonth || $day > $daysInMonth) { ?>
            <td class="empty"></td>
        <?php
                } else {
                    $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
                    $isBooked = isRoomBooked($bookings, $roomId, $date); ?>

            <td class="day <?php echo $isBooked ? 'booked' : 'available' ?>"
                data-date="<?php echo $date ?>">
                <?php echo $day ?>
            </td>
    <?php
                    $day++;
                }
                $cellCount++;
            endfor;
    ?>
        </tr>
    </tbody>
</table>