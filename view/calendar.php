<?php require __DIR__ . '/../src/backend/calendar.php'; ?>
<form method="POST">
    <input type="hidden" name="selected_date" id="selected-date">
    <input type="hidden" name="arrival_date" id="arrival-date">
    <input type="hidden" name="departure_date" id="departure-date">
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
                        $bookedRooms = getBookedRooms($bookings, $date);
                        $allRoomsBooked = count($bookedRooms) >= count($rooms); ?>

                <td class="day <?= $allRoomsBooked ? 'booked' : 'available' ?>"
                    data-date="<?= $date ?>">
                    <span class="date"><?= $day ?></span>
                </td>
        <?php
                        $day++;
                    }
                    $cellCount++;
                endfor;
        ?>
            </tr>
        </tbody>
</form>
</table>