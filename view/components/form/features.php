<?php
require __DIR__ . '/../../../src/functions/feature.functions.php';

if (isset($offerId)) { ?>
    <h2>
        <?php echo $offer['name'] ?>
    </h2>
<?php } else { ?>
    <h2>Activities</h2>
<?php } ?>

<div class="form-section">
    <?php
    if (isset($offerId)) {
        $groupedSpecs = groupFeatures($offerSpecs);
        $groupedSpecs = $groupedSpecs['hotel-specific'];
    }

    $groupedFeatures = groupFeatures($featuresInfo);

    ?>

    <div class="feature-container">
        <?php foreach ($groupedFeatures as $category => $features) { ?>
            <div class="feature-card">
                <span class="category-name">
                    <?php echo $category === 'hotel-specific' ? 'spa' : $category; ?>
                </span>
                <?php foreach ($features as $feature) {
                    $isIncluded = false;

                    if (isset($groupedSpecs)) {
                        foreach ($groupedSpecs as $spec) {
                            if ($spec['id'] === $feature['id']) {
                                $isIncluded = true;
                                break;
                            }
                        }
                    }
                ?>
                    <div class="feature-items">
                        <input
                            type="checkbox"
                            name="feature_ids[]"
                            id="feature_<?php echo $feature['id']; ?>"
                            value="<?php echo $feature['id']; ?>"
                            data-price="<?php echo $feature['price']; ?>"
                            <?php if ($isIncluded) {
                                echo 'checked disabled';
                            } ?>>

                        <?php if ($isIncluded) { ?>
                            <input
                                type="hidden"
                                name="feature_ids[]"
                                value="<?= $feature['id'] ?>">
                        <?php }; ?>

                        <label for=" feature_<?php echo $feature['id']; ?>">
                            <span class="feature-name">
                                <?php echo $feature['name']; ?>
                            </span>
                            <span>
                                $
                                <span class="feature-price">
                                    <?php echo $feature['price']; ?>
                                </span>
                            </span>
                        </label>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

    </div>

    <div class="cost-display">
        <span>Cost: </span><span>$ <span id="feature-cost"></span></span>
    </div>
</div>