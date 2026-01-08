<h2>Payment</h2>
<div class="form-section">
    <div class="guest-info">
        <!-- Payment selection -->
        <label>
            <input
                type="radio"
                name="payment_method"
                value="api_key"
                checked
                <?php ($old['payment_method'] ?? 'api_key') === 'api_key' ? 'checked' : 'checked'; ?>>
            Pay with API-Key
        </label>
        <label>
            <input
                type="radio"
                name="payment_method"
                value="transfer_code"
                <?php ($old['payment_method'] ?? '') === 'transfer_code' ? 'checked' : ''; ?>>
            Pay with TransferCode
        </label>
    </div>

    <!-- Input fields for payment -->
    <div class="guest-info" id="api-key-field">
        <label for="api_key">
            Enter your API-Key:
        </label>
        <input type="text"
            id="api_key"
            name="api_key"
            placeholder="uuid-string">
    </div>

    <div class="guest-info" id="transfer-code-field" style="display:none;">
        <label for="transfer_code">Enter your TransferCode:</label>
        <input
            type="text"
            id="transfer_code"
            name="transfer_code"
            placeholder="uuid-transfer-code">
    </div>
</div>

<!-- Extra field for discount if offer is active -->
<?php if (isset($offerId) && !empty($offerId)) { ?>
    <div class="cost-display discount-display">
        <span>Discount: </span><span>- $<span id="discount-cost"><?php echo $offerDiscount; ?></span></span>
    </div>
    <!-- Discount info for controllers -->
    <input
        type="hidden"
        id="offer_discount"
        name="offer_discount"
        value="<?php echo $offerDiscount; ?>">
<?php } ?>
<div class="cost-display">
    <span>Total: </span><span>$ <span id="total-cost"></span></span>
</div>
<!-- Room information for controllers price handling-->
<input
    type="hidden"
    id="room_id"
    name="room_id"
    value="<?php echo $room['id']; ?>">

<button class="btn action-btn" type="submit">Book</button>