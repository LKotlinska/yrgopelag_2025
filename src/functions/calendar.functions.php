<?php

declare(strict_types=1);

function isRoomBooked(array $bookings, int $roomId, string $date): bool
{
    foreach ($bookings as $booking) {
        if (
            (int) $booking['room_id'] === $roomId &&
            $date >= $booking['arrival_date'] &&
            $date < $booking['departure_date']
        ) {
            return true;
        }
    }
    return false;
}

function renderCalendar(
    int $cells,
    string $firstDayOfMonth,
    int $daysInMonth,
    int $day,
    int $year,
    int $month,
    array $bookings,
    int $roomId
): void { ?>
    <aside class="calendar-container">
        <h1>
            Availability
        </h1>
        <table id="calendar">
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

                <?php } else {
                            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
                            $isBooked = isRoomBooked($bookings, $roomId, $date); ?>

                    <td class="day <?php echo $isBooked ? 'booked' : 'available' ?>"
                        data-date="<?php echo $date ?>">
                        <?php echo $day ?>
                    </td>

            <?php $day++;
                        }
                        $cellCount++;
                    endfor; ?>
                </tr>
            </tbody>
        </table>

        <div class="calendar-tip">
            <div class="tip-items">
                <div class="tip-item booked"></div>
                Fully booked
            </div>
            <div class="tip-items">
                <div class="tip-item available"></div>
                Room available
            </div>
        </div>
    </aside>
<?php }
