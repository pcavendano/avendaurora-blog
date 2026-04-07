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
            <img src="<?= $heroImage->thumb(['width' => 1440])->url() ?>"
                 srcset="<?= $heroImage->srcset([800, 1024, 1440, 2048]) ?>"
                 sizes="100vw"
                 alt="<?= $page->hero_title() ?>"
                 class="hero__fullwidth-image">
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
            <!-- Antojitos: taco -->
            <a href="<?= url('recetas') ?>?category=antojitos" class="category-card">
                <span class="category-card__icon">
                    <svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 38c0 0 4-22 18-22s18 22 18 22" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M10 38c0 3 8 5 18 5s18-2 18-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <circle cx="20" cy="30" r="2.5" fill="var(--color-secondary)"/>
                        <circle cx="28" cy="26" r="2" fill="var(--color-secondary)"/>
                        <circle cx="34" cy="31" r="2.5" fill="var(--color-secondary)"/>
                        <path d="M24 22c-1-3 0-6 3-7" stroke="var(--color-secondary)" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M30 20c1-3 3-5 5-4" stroke="var(--color-secondary)" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </span>
                <span class="category-card__title"><?= t('category.antojitos') ?></span>
            </a>

            <!-- Platos Fuertes: chicken leg -->
            <a href="<?= url('recetas') ?>?category=platos-fuertes" class="category-card">
                <span class="category-card__icon">
                    <svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M32 12c-8-2-16 4-17 12s3 14 8 18c2 2 3 4 3 6v2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M26 50h8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M30 50v-6c0-2 2-4 4-6 4-4 7-10 6-17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <ellipse cx="30" cy="24" rx="8" ry="10" fill="var(--color-secondary)" opacity="0.25"/>
                        <path d="M36 14c3 2 5 6 4 11" stroke="var(--color-secondary)" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </span>
                <span class="category-card__title"><?= t('category.platos-fuertes') ?></span>
            </a>

            <!-- Sopas y Caldos: steaming bowl -->
            <a href="<?= url('recetas') ?>?category=sopas-caldos" class="category-card">
                <span class="category-card__icon">
                    <svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 30h32" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M14 30c1 10 6 16 14 16s13-6 14-16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M20 14c0 4-3 4-3 8" stroke="var(--color-secondary)" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M28 12c0 4-3 4-3 8" stroke="var(--color-secondary)" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M36 14c0 4-3 4-3 8" stroke="var(--color-secondary)" stroke-width="1.5" stroke-linecap="round"/>
                        <ellipse cx="28" cy="38" rx="8" ry="4" fill="var(--color-secondary)" opacity="0.2"/>
                    </svg>
                </span>
                <span class="category-card__title"><?= t('category.sopas-caldos') ?></span>
            </a>

            <!-- Mariscos: shrimp -->
            <a href="<?= url('recetas') ?>?category=mariscos" class="category-card">
                <span class="category-card__icon">
                    <svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M38 18c-4-4-12-4-16 2s-2 14 4 18l4 2" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M30 40c2 1 4 3 6 3s4-1 4-3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M38 18c2-1 5-1 6 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M36 16c1-2 4-3 6-2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M24 22c-3 3-4 8-2 13" stroke="var(--color-secondary)" stroke-width="1.5" stroke-linecap="round"/>
                        <circle cx="34" cy="22" r="1.5" fill="var(--color-secondary)"/>
                        <path d="M18 32c-2 1-4 1-5 0" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M20 36c-2 2-5 2-6 1" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </span>
                <span class="category-card__title"><?= t('category.mariscos') ?></span>
            </a>

            <!-- Postres: flan -->
            <a href="<?= url('recetas') ?>?category=postres" class="category-card">
                <span class="category-card__icon">
                    <svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M16 36c0-12 5-20 12-20s12 8 12 20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <ellipse cx="28" cy="36" rx="12" ry="4" stroke="currentColor" stroke-width="1.8"/>
                        <path d="M16 36c2 3 4 5 6 4s3-3 6-3 4 2 6 3 4-1 6-4" stroke="var(--color-secondary)" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M40 38c1 2 1 4-1 5-3 2-8 3-11 3s-8-1-11-3c-2-1-2-3-1-5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" opacity="0.4"/>
                    </svg>
                </span>
                <span class="category-card__title"><?= t('category.postres') ?></span>
            </a>

            <!-- Bebidas: glass with lime -->
            <a href="<?= url('recetas') ?>?category=bebidas" class="category-card">
                <span class="category-card__icon">
                    <svg width="56" height="56" viewBox="0 0 56 56" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 16l3 26h14l3-26" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M16 16h24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        <path d="M20 28h16" stroke="var(--color-secondary)" stroke-width="1.5" stroke-linecap="round" opacity="0.5"/>
                        <circle cx="38" cy="20" r="5" stroke="var(--color-secondary)" stroke-width="1.5"/>
                        <path d="M38 20l3-3" stroke="var(--color-secondary)" stroke-width="1.5" stroke-linecap="round"/>
                        <path d="M36 20h4" stroke="var(--color-secondary)" stroke-width="1.2" stroke-linecap="round"/>
                        <path d="M21 34c2-1 5-1 7 0s5 1 7 0" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" opacity="0.4"/>
                    </svg>
                </span>
                <span class="category-card__title"><?= t('category.bebidas') ?></span>
            </a>
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