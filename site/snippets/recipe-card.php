<?php
/**
 * Recipe Card Snippet
 *
 * @var Page $recipe
 */
?>
<article class="recipe-card" data-category="<?= $recipe->category() ?>">
    <a href="<?= $recipe->url() ?>" class="recipe-card__link">
        <!-- Image -->
        <div class="recipe-card__image">
            <?php if ($cover = $recipe->cover()->toFile()): ?>
                <img src="<?= $cover->thumb(['width' => 600, 'height' => 400, 'crop' => true])->url() ?>"
                     srcset="<?= $cover->srcset([300, 450, 600, 900]) ?>"
                     sizes="(max-width: 600px) 100vw, (max-width: 1024px) 50vw, 33vw"
                     alt="<?= $recipe->title() ?>"
                     loading="lazy">
            <?php elseif ($recipe->original_image()->isNotEmpty()): ?>
                <img src="<?= $recipe->original_image() ?>"
                     alt="<?= $recipe->title() ?>"
                     loading="lazy">
            <?php else: ?>
                <div class="recipe-card__placeholder">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                        <path d="M3 2v7c0 1.1.9 2 2 2h4a2 2 0 0 0 2-2V2"/>
                        <path d="M7 2v20"/>
                        <path d="M21 15V2v0a5 5 0 0 0-5 5v6c0 1.1.9 2 2 2h3Zm0 0v7"/>
                    </svg>
                </div>
            <?php endif ?>

            <!-- Category Tag -->
            <span class="recipe-card__category">
                <?= t('category.' . $recipe->category()) ?>
            </span>
        </div>

        <!-- Content -->
        <div class="recipe-card__content">
            <h3 class="recipe-card__title"><?= $recipe->title() ?></h3>

            <?php if ($recipe->description()->isNotEmpty()): ?>
                <p class="recipe-card__description"><?= $recipe->description()->excerpt(100) ?></p>
            <?php endif ?>

            <!-- Meta -->
            <div class="recipe-card__meta">
                <?php if ($recipe->total_time()->isNotEmpty()): ?>
                    <span class="recipe-card__time">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12,6 12,12 16,14"/>
                        </svg>
                        <?= $recipe->total_time() ?> min
                    </span>
                <?php endif ?>

                <?php if ($recipe->difficulty()->isNotEmpty()): ?>
                    <span class="recipe-card__difficulty recipe-card__difficulty--<?= $recipe->difficulty() ?>">
                        <?= t('recipe.difficulty.' . $recipe->difficulty()) ?>
                    </span>
                <?php endif ?>
            </div>
        </div>
    </a>

    <!-- Kit Price (if available) -->
    <?php if ($recipe->enable_kits()->toBool() && $recipe->store_kits()->isNotEmpty()): ?>
        <?php $firstKit = $recipe->store_kits()->toStructure()->first(); ?>
        <?php if ($firstKit && $firstKit->kit_price()->isNotEmpty()): ?>
            <div class="recipe-card__kit">
                <span class="recipe-card__kit-label"><?= t('kit.title') ?></span>
                <span class="recipe-card__kit-price">$<?= number_format($firstKit->kit_price()->toFloat(), 2) ?></span>
            </div>
        <?php endif ?>
    <?php endif ?>
</article>
