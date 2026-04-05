<?php
/**
 * Ingredient Card Snippet
 *
 * @var Page $ingredient
 */
?>
<article class="ingredient-card">
    <a href="<?= $ingredient->url() ?>" class="ingredient-card__link">
        <!-- Image -->
        <div class="ingredient-card__image">
            <?php if ($cover = $ingredient->cover()->toFile()): ?>
                <img src="<?= $cover->thumb(['width' => 300, 'height' => 300, 'crop' => true])->url() ?>"
                     alt="<?= $ingredient->title() ?>"
                     loading="lazy">
            <?php endif ?>
        </div>

        <!-- Content -->
        <div class="ingredient-card__content">
            <h3 class="ingredient-card__title"><?= $ingredient->title() ?></h3>

            <?php if ($ingredient->also_known_as()->isNotEmpty()): ?>
                <p class="ingredient-card__aka">
                    <?= $ingredient->also_known_as()->split(',')[0] ?? '' ?>
                </p>
            <?php endif ?>

            <!-- Heat Level (for chiles) -->
            <?php if ($ingredient->category()->value() === 'chiles' && $ingredient->heat_level()->isNotEmpty()): ?>
                <div class="ingredient-card__heat">
                    <span class="ingredient-card__heat-label"><?= t('ingredient.heat_level') ?>:</span>
                    <div class="ingredient-card__heat-bar">
                        <div class="ingredient-card__heat-fill" style="width: <?= $ingredient->heat_level()->toInt() * 10 ?>%"></div>
                    </div>
                    <span class="ingredient-card__heat-value"><?= $ingredient->heat_level() ?>/10</span>
                </div>
            <?php endif ?>

            <!-- Relationship hint -->
            <?php if ($ingredient->related_ingredients()->isNotEmpty()): ?>
                <p class="ingredient-card__relation">
                    <?= t('ingredient.related') ?>: <?= $ingredient->related_ingredients()->toPages()->first()->title() ?>
                </p>
            <?php endif ?>
        </div>
    </a>
</article>
