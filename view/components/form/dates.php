    <h2>Dates</h2>
    <div class="form-section">
        <div class="date-select">
            <div class="date-item">
                <label for="arrival_date">
                    Arrival
                </label>
                <input
                    type="date"
                    id="arrival_date"
                    name="arrival_date"
                    min="2026-01-01"
                    max="2026-01-31"
                    required
                    value="<?php echo $old['arrival_date'] ?? '' ?>" />
                <span>Check-in from 15:00</span>
            </div>
            <div class="date-item">
                <label for="departure_date">
                    Departure
                </label>
                <input
                    type="date"
                    id="departure_date"
                    name="departure_date"
                    min="2026-01-01"
                    max="2026-01-31"
                    required
                    value="<?php echo $old['departure_date'] ?? '' ?>" />
                <span>Check-out by 11:00</span>
            </div>
        </div>
        <div class="cost-display">
            <span>Cost: </span><span>$ <span id="room-cost"></span></span>
        </div>
    </div>