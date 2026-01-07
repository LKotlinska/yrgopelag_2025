<?php if (!empty($errors)) {
    foreach ($errors as $error) : ?>
        <div id="error_msgs">
            <div class="msg-card error-s">
                <p>
                    <span class="material-symbols-outlined">
                        error
                    </span><?php echo $error; ?>
                </p>
            </div>
        </div>
<?php endforeach;
} ?>