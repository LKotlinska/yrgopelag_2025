<?php require __DIR__ . '/../src/backend/calendar.php'; ?>
<form method="POST">
    <input type="hidden" name="selected_date" id="selected-date">
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
            <?php for ($cell = 1; $cell <= $cells; $cell++): ?>

                <?php if ($cell < $firstDayOfMonth || $day > $daysInMonth): ?>
                    <td class="empty"></td>

                <?php else: ?>
                    <?php $date = sprintf('%04d-01-%02d', $year, $day); ?>
                    <td class="day" data-date="<?= $date ?>">
                        <span class="date"><?= $day ?></span>

                        <div class="rooms">
                            <?php for ($room = 1; $room <= 3; $room++): ?>
                                <span class="room <?= ($availability[$room] ?? 1) ? 'available' : 'booked' ?>"></span>
                            <?php endfor; ?>
                        </div>
                    </td>
                    <?php $day++; ?>
                <?php endif; ?>

                <?php if ($cell % 7 === 0 && $cell < $cells): ?>
                    </tr>
                    <tr>
                    <?php endif; ?>

                <?php endfor; ?>
                    </tr>
                    <input type="hidden"
                        name="selected-date"
                        id="selected-date">
                    <button type="submit">Submit</button>
        </tbody>
</form>
</table>