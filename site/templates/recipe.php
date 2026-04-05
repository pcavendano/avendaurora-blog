<?php snippet('header') ?>

<article class="recipe" itemscope itemtype="https://schema.org/Recipe">
    <!-- Breadcrumb -->
    <nav class="breadcrumb">
        <div class="container">
            <a href="<?= $site->url() ?>"><?= t('nav.home') ?></a>
            <span>/</span>
            <a href="<?= page('recetas')->url() ?>"><?= t('nav.recipes') ?></a>
            <span>/</span>
            <a href="<?= page('recetas')->url() ?>?category=<?= $page->category() ?>"><?= t('category.' . $page->category()) ?></a>
            <span>/</span>
            <span><?= $page->title() ?></span>
        </div>
    </nav>

    <!-- Recipe Header -->
    <header class="recipe__header">
        <div class="container">
            <span class="recipe__category"><?= t('category.' . $page->category()) ?></span>
            <h1 class="recipe__title" itemprop="name"><?= $page->title() ?></h1>

            <?php if ($page->description()->isNotEmpty()): ?>
                <p class="recipe__description" itemprop="description"><?= $page->description() ?></p>
            <?php endif ?>

            <!-- Recipe Actions -->
            <div class="recipe__actions">
                <button class="recipe__action" onclick="window.print()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 9V2h12v7"/>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                        <rect x="6" y="14" width="12" height="8"/>
                    </svg>
                    <?= t('recipe.print') ?>
                </button>
                <button class="recipe__action" onclick="shareRecipe()">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="18" cy="5" r="3"/>
                        <circle cx="6" cy="12" r="3"/>
                        <circle cx="18" cy="19" r="3"/>
                        <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/>
                        <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/>
                    </svg>
                    <?= t('recipe.share') ?>
                </button>
            </div>
        </div>
    </header>

    <!-- Hero Image -->
    <?php if ($cover = $page->cover()->toFile()): ?>
    <div class="recipe__hero">
        <div class="container">
            <img src="<?= $cover->url() ?>"
                 alt="<?= $page->title() ?>"
                 itemprop="image"
                 class="recipe__hero-image">
        </div>
    </div>
    <?php elseif ($page->original_image()->isNotEmpty()): ?>
    <div class="recipe__hero">
        <div class="container">
            <img src="<?= $page->original_image() ?>"
                 alt="<?= $page->title() ?>"
                 itemprop="image"
                 class="recipe__hero-image">
        </div>
    </div>
    <?php endif ?>

    <!-- Recipe Meta -->
    <div class="recipe__meta">
        <div class="container">
            <div class="recipe__meta-grid">
                <?php if ($page->prep_time()->isNotEmpty()): ?>
                <div class="recipe__meta-item">
                    <span class="recipe__meta-label"><?= t('recipe.prep_time') ?></span>
                    <span class="recipe__meta-value" itemprop="prepTime" content="PT<?= $page->prep_time() ?>M">
                        <?= $page->prep_time() ?> min
                    </span>
                </div>
                <?php endif ?>

                <?php if ($page->cook_time()->isNotEmpty()): ?>
                <div class="recipe__meta-item">
                    <span class="recipe__meta-label"><?= t('recipe.cook_time') ?></span>
                    <span class="recipe__meta-value" itemprop="cookTime" content="PT<?= $page->cook_time() ?>M">
                        <?= $page->cook_time() ?> min
                    </span>
                </div>
                <?php endif ?>

                <?php if ($page->total_time()->isNotEmpty()): ?>
                <div class="recipe__meta-item">
                    <span class="recipe__meta-label"><?= t('recipe.total_time') ?></span>
                    <span class="recipe__meta-value" itemprop="totalTime" content="PT<?= $page->total_time() ?>M">
                        <?= $page->total_time() ?> min
                    </span>
                </div>
                <?php endif ?>

                <?php if ($page->servings()->isNotEmpty()): ?>
                <div class="recipe__meta-item">
                    <span class="recipe__meta-label"><?= t('recipe.servings') ?></span>
                    <span class="recipe__meta-value" itemprop="recipeYield">
                        <?= $page->servings() ?>
                    </span>
                </div>
                <?php endif ?>

                <?php if ($page->difficulty()->isNotEmpty()): ?>
                <div class="recipe__meta-item">
                    <span class="recipe__meta-label"><?= t('recipe.difficulty') ?></span>
                    <span class="recipe__meta-value recipe__difficulty--<?= $page->difficulty() ?>">
                        <?= t('recipe.difficulty.' . $page->difficulty()) ?>
                    </span>
                </div>
                <?php endif ?>
            </div>
        </div>
    </div>

    <!-- Recipe Content -->
    <div class="recipe__content">
        <div class="container">
            <div class="recipe__grid">
                <!-- Main Content -->
                <div class="recipe__main">
                    <!-- Ingredients -->
                    <section class="recipe__section recipe__ingredients">
                        <h2 class="recipe__section-title"><?= t('recipe.ingredients') ?></h2>
                        <ul class="ingredients-list">
                            <?php foreach ($page->ingredients()->toStructure() as $item): ?>
                            <li class="ingredients-list__item" itemprop="recipeIngredient">
                                <label class="ingredient">
                                    <input type="checkbox" class="ingredient__checkbox">
                                    <span class="ingredient__text">
                                        <?php if ($item->quantity()->isNotEmpty()): ?>
                                            <strong><?= $item->quantity() ?></strong>
                                        <?php endif ?>
                                        <?php if ($item->unit()->isNotEmpty()): ?>
                                            <?= $item->unit() ?>
                                        <?php endif ?>
                                        <?= $item->ingredient() ?>
                                        <?php if ($item->preparation()->isNotEmpty()): ?>
                                            <em>(<?= $item->preparation() ?>)</em>
                                        <?php endif ?>
                                        <?php if ($item->optional()->toBool()): ?>
                                            <span class="ingredient__optional">(opcional)</span>
                                        <?php endif ?>
                                    </span>
                                </label>
                                <?php if ($ingredientPage = $item->ingredient_link()->toPage()): ?>
                                    <a href="<?= $ingredientPage->url() ?>" class="ingredient__link" title="Ver más sobre <?= $ingredientPage->title() ?>">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <circle cx="12" cy="12" r="10"/>
                                            <path d="M12 16v-4"/>
                                            <path d="M12 8h.01"/>
                                        </svg>
                                    </a>
                                <?php endif ?>
                            </li>
                            <?php endforeach ?>
                        </ul>
                    </section>

                    <!-- Instructions -->
                    <section class="recipe__section recipe__instructions">
                        <h2 class="recipe__section-title"><?= t('recipe.instructions') ?></h2>
                        <ol class="instructions-list" itemprop="recipeInstructions">
                            <?php foreach ($page->instructions()->toStructure() as $i => $step): ?>
                            <li class="instructions-list__item">
                                <div class="instruction">
                                    <span class="instruction__number"><?= $i + 1 ?></span>
                                    <div class="instruction__content">
                                        <?php if ($step->step_title()->isNotEmpty()): ?>
                                            <h3 class="instruction__title"><?= $step->step_title() ?></h3>
                                        <?php endif ?>
                                        <p class="instruction__text"><?= $step->instruction() ?></p>
                                        <?php if ($step->tip()->isNotEmpty()): ?>
                                            <div class="instruction__tip">
                                                <strong>💡 Tip:</strong> <?= $step->tip() ?>
                                            </div>
                                        <?php endif ?>
                                        <?php if ($stepImage = $step->step_image()->toFile()): ?>
                                            <img src="<?= $stepImage->thumb(['width' => 600])->url() ?>"
                                                 alt="Paso <?= $i + 1 ?>"
                                                 class="instruction__image"
                                                 loading="lazy">
                                        <?php endif ?>
                                    </div>
                                </div>
                            </li>
                            <?php endforeach ?>
                        </ol>
                    </section>

                    <!-- Chef's Tips -->
                    <?php if ($page->tips()->isNotEmpty()): ?>
                    <section class="recipe__section recipe__tips">
                        <h2 class="recipe__section-title"><?= t('recipe.tips') ?></h2>
                        <div class="tips-content">
                            <?= $page->tips()->kt() ?>
                        </div>
                    </section>
                    <?php endif ?>

                    <!-- History -->
                    <?php if ($page->history()->isNotEmpty()): ?>
                    <section class="recipe__section recipe__history">
                        <h2 class="recipe__section-title"><?= t('recipe.history') ?></h2>
                        <div class="history-content">
                            <?= $page->history()->kt() ?>
                        </div>
                    </section>
                    <?php endif ?>
                </div>

                <!-- Sidebar -->
                <aside class="recipe__sidebar">
                    <!-- Ingredient Kit -->
                    <?php if ($page->enable_kits()->toBool() && $page->store_kits()->isNotEmpty()): ?>
                    <div class="recipe__kit-box">
                        <h3 class="recipe__kit-title"><?= t('kit.title') ?></h3>
                        <?php foreach ($page->store_kits()->toStructure() as $kit): ?>
                            <?php $store = $kit->store()->toPage(); ?>
                            <?php if ($store): ?>
                            <div class="kit-option">
                                <div class="kit-option__header">
                                    <span class="kit-option__store"><?= $store->title() ?></span>
                                    <span class="kit-option__availability"><?= $kit->availability() ?>% disponible</span>
                                </div>
                                <?php if ($kit->missing_items()->isNotEmpty()): ?>
                                    <p class="kit-option__missing">
                                        <small><?= t('kit.missing') ?>: <?= $kit->missing_items() ?></small>
                                    </p>
                                <?php endif ?>
                                <div class="kit-option__footer">
                                    <span class="kit-option__price">$<?= number_format($kit->kit_price()->toFloat(), 2) ?></span>
                                    <?php if ($kit->kit_link()->isNotEmpty()): ?>
                                        <a href="<?= $kit->kit_link() ?>" class="btn btn--small btn--primary" target="_blank">
                                            <?= t('kit.add_to_cart') ?>
                                        </a>
                                    <?php endif ?>
                                </div>
                            </div>
                            <?php endif ?>
                        <?php endforeach ?>
                    </div>
                    <?php endif ?>

                    <!-- Related Recipes -->
                    <?php
                    $related = page('recetas')->children()
                        ->listed()
                        ->filterBy('category', $page->category())
                        ->not($page)
                        ->shuffle()
                        ->limit(3);
                    ?>
                    <?php if ($related->isNotEmpty()): ?>
                    <div class="recipe__related">
                        <h3 class="recipe__related-title"><?= t('recipe.related') ?></h3>
                        <div class="related-recipes">
                            <?php foreach ($related as $recipe): ?>
                                <a href="<?= $recipe->url() ?>" class="related-recipe">
                                    <?php if ($cover = $recipe->cover()->toFile()): ?>
                                        <img src="<?= $cover->thumb(['width' => 100, 'height' => 100, 'crop' => true])->url() ?>"
                                             alt="<?= $recipe->title() ?>">
                                    <?php elseif ($recipe->original_image()->isNotEmpty()): ?>
                                        <img src="<?= $recipe->original_image() ?>"
                                             alt="<?= $recipe->title() ?>"
                                             style="width:100px;height:100px;object-fit:cover;">
                                    <?php endif ?>
                                    <span><?= $recipe->title() ?></span>
                                </a>
                            <?php endforeach ?>
                        </div>
                    </div>
                    <?php endif ?>
                </aside>
            </div>
        </div>
    </div>
</article>

<script>
function shareRecipe() {
    if (navigator.share) {
        navigator.share({
            title: '<?= $page->title() ?>',
            text: '<?= $page->description() ?>',
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(window.location.href);
        alert('Link copiado!');
    }
}
</script>

<?php snippet('footer') ?>
