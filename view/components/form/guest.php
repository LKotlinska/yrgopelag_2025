<h2>Guest information </h2>
<div class="form-section">
    <div class="guest-info">
        <label for="name">
            Enter your name:
        </label>
        <input type="text"
            id="name"
            name="name"
            placeholder="e.g. Rune"
            required
            value="<?php echo $old['name'] ?? '' ?>">
    </div>
</div>