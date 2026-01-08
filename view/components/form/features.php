<?php
require __DIR__ . '/../../../src/functions/feature.functions.php';

require __DIR__ . '/../../../src/controllers/offers.php';

$groupedFeatures = groupFeatures($featuresInfo);
$oldFeatureIds = array_map('intval', $old['feature_ids'] ?? []);

if (isset($offerId)) { ?>
    <h2>
        <?php echo $offer['name'] ?>
    </h2>
<?php } else { ?>
    <h2>Activities</h2>
<?php } ?>

<div class="form-section">
    <div class="feature-container">
        <?php foreach ($groupedFeatures as $category => $features) { ?>
            <div class="feature-card">
                <span class="category-name">
                    <?php echo $category === 'hotel-specific' ? 'spa' : $category; ?>
                    <span class="field-req-tip">(optional)</span>
                </span>
                <?php
                foreach ($features as $feature) {
                    $isIncluded = false;
                    if (isset($offerSpecs)) {
                        foreach ($offerSpecs as $category => $spec) {

                            if ($spec['id'] === $feature['id']) {
                                $isIncluded = true;
                                break;
                            }
                        }
                    }
                    $isPreviouslySelected = in_array(
                        (int) $feature['id'],
                        $oldFeatureIds,
                        true
                    );
                ?>
                    <div class="feature-items">
                        <input
                            type="checkbox"
                            name="feature_ids[]"
                            id="feature_<?php echo $feature['id']; ?>"
                            value="<?php echo $feature['id']; ?>"
                            data-price="<?php echo $feature['price']; ?>"
                            <?php
                            // Check the input if offer is active
                            if ($isIncluded) {
                                echo 'checked disabled';
                                // Check the input if it was selected when error occured
                            } elseif ($isPreviouslySelected) {
                                echo 'checked';
                            }
                            ?> />

                        <?php if ($isIncluded) { ?>
                            <input
                                type="hidden"
                                name="feature_ids[]"
                                value="<?= $feature['id'] ?>">
                        <?php }; ?>

                        <label for="feature_<?php echo $feature['id']; ?>">
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