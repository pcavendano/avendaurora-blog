<?php snippet('header') ?>

<!-- Hero Section (GBC-Style) -->
<section class="hero">
    <div class="hero__brand-section">
        <h1 class="hero__name"><?= $page->hero_title()->or('Aurora') ?></h1>
        <?php if ($page->hero_subtitle()->isNotEmpty()): ?>
            <p class="hero__tagline"><?= $page->hero_subtitle() ?></p>
        <?php endif ?>
    </div>

    <?php if ($heroImage = $page->hero_image()->toFile()): ?>
        <div class="hero__image-wrapper">
            <img src="<?= $heroImage->url() ?>" alt="<?= $page->hero_title() ?>" class="hero__fullwidth-image">
        </div>
    <?php endif ?>

    <?php if ($page->hero_description()->isNotEmpty()): ?>
        <div class="hero__description-wrapper">
            <p class="hero__description"><?= $page->hero_description() ?></p>
        </div>
    <?php endif ?>

    <div class="hero__ctas">
        <a href="<?= $page->hero_cta_link()->toPage() ? $page->hero_cta_link()->toPage()->url() : url('about') ?>"
           class="hero__cta btn btn--primary">
            <?= $page->hero_cta_text()->or('Conoce Mi Historia') ?>
        </a>
        <a href="<?= url('recetas') ?>" class="hero__cta btn btn--outline">
            <?= t('nav.mi_cocina') ?> &rarr;
        </a>
    </div>
</section>

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
                <?php if ($page->about_subtitle()->isNotEmpty()): ?>
                    <p class="about-preview__subtitle"><?= $page->about_subtitle() ?></p>
                <?php endif ?>
                <div class="about-preview__text"><?= $page->about_text()->kt() ?></div>
                <a href="<?= url('about') ?>" class="btn btn--outline"><?= t('general.read_more') ?> &rarr;</a>
            </div>
        </div>
    </div>
</section>
<?php endif ?>

<!-- Featured Recipes / Mi Cocina -->
<section class="section section--recipes">
    <div class="container">
        <header class="section__header">
            <h2 class="section__title"><?= $page->featured_title()->or(t('home.from_my_kitchen')) ?></h2>
            <a href="<?= url('recetas') ?>" class="section__link"><?= t('general.view_all') ?> &rarr;</a>
        </header>

        <div class="recipe-grid">
            <?php
            $recipes = $page->featured_recipes()->toPages();
            if ($recipes->isEmpty()) {
                $recipes = page('recetas')->children()->sortBy('title', 'asc')->limit(8);
            }
            foreach ($recipes as $recipe):
                snippet('recipe-card', ['recipe' => $recipe]);
            endforeach;
            ?>
        </div>
    </div>
</section>

<!-- Categories -->
<?php if ($page->show_categories()->toBool(true)): ?>
<section class="section section--categories section--alt">
    <div class="container">
        <header class="section__header">
            <h2 class="section__title"><?= $page->categories_title()->or('Explora por Categoria') ?></h2>
        </header>

        <div class="category-grid">
            <?php
            $categories = [
                'antojitos' => ['icon' => '🌮'],
                'platos-fuertes' => ['icon' => '🍖'],
                'sopas-caldos' => ['icon' => '🍲'],
                'mariscos' => ['icon' => '🦐'],
                'postres' => ['icon' => '🍮'],
                'bebidas' => ['icon' => '🍹'],
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

<!-- Featured Ingredient -->
<?php if ($featuredIngredient = $page->featured_ingredient()->toPage()): ?>
<section class="section section--ingredient-highlight">
    <div class="container">
        <div class="ingredient-highlight">
            <div class="ingredient-highlight__icon">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
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

<?php snippet('footer') ?>