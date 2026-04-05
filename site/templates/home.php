<?php snippet('header') ?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero__container">
        <div class="hero__content">
            <h1 class="hero__title"><?= $page->hero_title()->or($site->title()) ?></h1>
            <?php if ($page->hero_subtitle()->isNotEmpty()): ?>
                <p class="hero__subtitle"><?= $page->hero_subtitle() ?></p>
            <?php endif ?>
            <?php if ($page->hero_description()->isNotEmpty()): ?>
                <p class="hero__description"><?= $page->hero_description() ?></p>
            <?php endif ?>
            <a href="<?= $page->hero_cta_link()->toPage() ? $page->hero_cta_link()->toPage()->url() : url('recetas') ?>"
               class="hero__cta btn btn--primary">
                <?= $page->hero_cta_text()->or(t('nav.recipes')) ?>
            </a>
        </div>
        <?php if ($heroImage = $page->hero_image()->toFile()): ?>
            <div class="hero__image">
                <img src="<?= $heroImage->url() ?>" alt="<?= $page->hero_title() ?>">
            </div>
        <?php endif ?>
    </div>
</section>

<!-- Featured Recipes -->
<section class="section section--recipes">
    <div class="container">
        <header class="section__header">
            <h2 class="section__title">Recetas Recientes</h2>
            <a href="<?= url('recetas') ?>" class="section__link"><?= t('general.view_all') ?> &rarr;</a>
        </header>

        <div class="recipe-grid">
            <?php
            $recipes = $page->featured_recipes()->toPages();
            if ($recipes->isEmpty()) {
                $recipes = page('recetas')->children()->listed()->sortBy('date', 'desc')->limit(8);
            }
            foreach ($recipes as $recipe):
                snippet('recipe-card', ['recipe' => $recipe]);
            endforeach;
            ?>
        </div>
    </div>
</section>

<!-- Featured Ingredient (Did You Know) -->
<?php if ($featuredIngredient = $page->featured_ingredient()->toPage()): ?>
<section class="section section--ingredient-highlight">
    <div class="container">
        <div class="ingredient-highlight">
            <div class="ingredient-highlight__icon">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 16v-4"/>
                    <path d="M12 8h.01"/>
                </svg>
            </div>
            <div class="ingredient-highlight__content">
                <h3 class="ingredient-highlight__title"><?= t('ingredient.did_you_know') ?></h3>
                <?php if ($featuredIngredient->relationship_explanation()->isNotEmpty()): ?>
                    <p class="ingredient-highlight__text"><?= $featuredIngredient->relationship_explanation() ?></p>
                <?php else: ?>
                    <p class="ingredient-highlight__text"><?= $featuredIngredient->description()->excerpt(200) ?></p>
                <?php endif ?>
                <a href="<?= $featuredIngredient->url() ?>" class="ingredient-highlight__link">
                    <?= t('general.read_more') ?> sobre <?= $featuredIngredient->title() ?> &rarr;
                </a>
            </div>
            <?php if ($cover = $featuredIngredient->cover()->toFile()): ?>
                <div class="ingredient-highlight__image">
                    <img src="<?= $cover->thumb(['width' => 300, 'height' => 300, 'crop' => true])->url() ?>"
                         alt="<?= $featuredIngredient->title() ?>">
                </div>
            <?php endif ?>
        </div>
    </div>
</section>
<?php endif ?>

<!-- Categories -->
<?php if ($page->show_categories()->toBool(true)): ?>
<section class="section section--categories">
    <div class="container">
        <header class="section__header">
            <h2 class="section__title">Explora por Categoría</h2>
        </header>

        <div class="category-grid">
            <?php
            $categories = [
                'antojitos' => ['icon' => '🌮', 'image' => 'antojitos.jpg'],
                'platos-fuertes' => ['icon' => '🍖', 'image' => 'platos-fuertes.jpg'],
                'sopas-caldos' => ['icon' => '🍲', 'image' => 'sopas.jpg'],
                'mariscos' => ['icon' => '🦐', 'image' => 'mariscos.jpg'],
                'postres' => ['icon' => '🍮', 'image' => 'postres.jpg'],
                'bebidas' => ['icon' => '🍹', 'image' => 'bebidas.jpg'],
            ];
            foreach ($categories as $slug => $data):
            ?>
            <a href="<?= url('recetas') ?>?category=<?= $slug ?>" class="category-card">
                <span class="category-card__icon"><?= $data['icon'] ?></span>
                <span class="category-card__title"><?= t('category.' . $slug) ?></span>
            </a>
            <?php endforeach ?>
        </div>
    </div>
</section>
<?php endif ?>

<!-- About Chef Section -->
<?php if ($page->about_title()->isNotEmpty()): ?>
<section class="section section--about">
    <div class="container">
        <div class="about-preview">
            <?php if ($aboutImage = $page->about_image()->toFile()): ?>
                <div class="about-preview__image">
                    <img src="<?= $aboutImage->thumb(['width' => 500, 'height' => 500, 'crop' => true])->url() ?>"
                         alt="<?= $page->about_title() ?>">
                </div>
            <?php endif ?>
            <div class="about-preview__content">
                <h2 class="about-preview__title"><?= $page->about_title() ?></h2>
                <p class="about-preview__text"><?= $page->about_text() ?></p>
                <a href="<?= url('about') ?>" class="btn btn--outline"><?= t('general.read_more') ?></a>
            </div>
        </div>
    </div>
</section>
<?php endif ?>

<!-- Partner Stores -->
<?php if ($page->show_stores()->toBool(true)): ?>
<?php $stores = page('tiendas')->children()->listed(); ?>
<?php if ($stores->isNotEmpty()): ?>
<section class="section section--stores">
    <div class="container">
        <header class="section__header">
            <h2 class="section__title">Tiendas Asociadas</h2>
            <p class="section__subtitle">Compra tus ingredientes en estas tiendas</p>
        </header>

        <div class="store-logos">
            <?php foreach ($stores as $store): ?>
                <a href="<?= $store->url() ?>" class="store-logo">
                    <?php if ($logo = $store->logo()->toFile()): ?>
                        <img src="<?= $logo->thumb(['width' => 150])->url() ?>" alt="<?= $store->title() ?>">
                    <?php else: ?>
                        <span><?= $store->title() ?></span>
                    <?php endif ?>
                </a>
            <?php endforeach ?>
        </div>
    </div>
</section>
<?php endif ?>
<?php endif ?>

<?php snippet('footer') ?>
