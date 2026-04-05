<?php snippet('header') ?>

<article class="ingredient-page">
    <!-- Breadcrumb -->
    <nav class="breadcrumb">
        <div class="container">
            <a href="<?= $site->url() ?>"><?= t('nav.home') ?></a>
            <span>/</span>
            <a href="<?= page('ingredientes')->url() ?>"><?= t('nav.ingredients') ?></a>
            <span>/</span>
            <span><?= $page->title() ?></span>
        </div>
    </nav>

    <!-- Header -->
    <header class="ingredient-page__header">
        <div class="container">
            <div class="ingredient-page__header-grid">
                <?php if ($cover = $page->cover()->toFile()): ?>
                <div class="ingredient-page__image">
                    <img src="<?= $cover->thumb(['width' => 400, 'height' => 400, 'crop' => true])->url() ?>"
                         alt="<?= $page->title() ?>">
                </div>
                <?php endif ?>

                <div class="ingredient-page__info">
                    <h1 class="ingredient-page__title"><?= $page->title() ?></h1>

                    <?php if ($page->also_known_as()->isNotEmpty()): ?>
                        <p class="ingredient-page__aka">
                            También conocido como: <?= $page->also_known_as() ?>
                        </p>
                    <?php endif ?>

                    <!-- Heat Level (for chiles) -->
                    <?php if ($page->category()->value() === 'chiles'): ?>
                    <div class="ingredient-page__heat">
                        <span class="ingredient-page__heat-label"><?= t('ingredient.heat_level') ?>:</span>
                        <div class="heat-meter">
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <span class="heat-meter__dot <?= $i <= $page->heat_level()->toInt() ? 'is-active' : '' ?>"></span>
                            <?php endfor ?>
                        </div>
                        <span class="ingredient-page__heat-value"><?= $page->heat_level() ?>/10</span>
                    </div>

                    <?php if ($page->scoville_min()->isNotEmpty() || $page->scoville_max()->isNotEmpty()): ?>
                    <p class="ingredient-page__scoville">
                        <?= t('ingredient.scoville') ?>:
                        <?= number_format($page->scoville_min()->toInt()) ?> - <?= number_format($page->scoville_max()->toInt()) ?> SHU
                    </p>
                    <?php endif ?>
                    <?php endif ?>

                    <!-- Available Forms -->
                    <?php if ($page->available_forms()->isNotEmpty()): ?>
                    <div class="ingredient-page__forms">
                        <span class="ingredient-page__forms-label"><?= t('ingredient.forms') ?>:</span>
                        <div class="form-tags">
                            <?php foreach ($page->available_forms()->split() as $form): ?>
                                <span class="form-tag"><?= t('form.' . $form) ?></span>
                            <?php endforeach ?>
                        </div>
                    </div>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </header>

    <div class="ingredient-page__content">
        <div class="container">
            <div class="ingredient-page__grid">
                <!-- Main Content -->
                <div class="ingredient-page__main">
                    <!-- Description -->
                    <?php if ($page->description()->isNotEmpty()): ?>
                    <section class="ingredient-section">
                        <h2 class="ingredient-section__title">Descripción</h2>
                        <div class="ingredient-section__content">
                            <?= $page->description()->kt() ?>
                        </div>
                    </section>
                    <?php endif ?>

                    <!-- RELATIONSHIP BOX - KEY FEATURE -->
                    <?php if ($page->related_ingredients()->isNotEmpty() && $page->relationship_explanation()->isNotEmpty()): ?>
                    <section class="ingredient-section ingredient-relationship">
                        <div class="relationship-box">
                            <div class="relationship-box__icon">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <path d="M12 16v-4"/>
                                    <path d="M12 8h.01"/>
                                </svg>
                            </div>
                            <h3 class="relationship-box__title"><?= t('ingredient.did_you_know') ?></h3>
                            <p class="relationship-box__text"><?= $page->relationship_explanation() ?></p>

                            <div class="relationship-box__visual">
                                <div class="relationship-visual">
                                    <span class="relationship-visual__item relationship-visual__item--current">
                                        <?= $page->title() ?>
                                    </span>
                                    <span class="relationship-visual__arrow">→</span>
                                    <?php foreach ($page->related_ingredients()->toPages() as $related): ?>
                                    <a href="<?= $related->url() ?>" class="relationship-visual__item relationship-visual__item--related">
                                        <?= $related->title() ?>
                                    </a>
                                    <?php endforeach ?>
                                </div>
                            </div>
                        </div>
                    </section>
                    <?php endif ?>

                    <!-- Flavor Profile -->
                    <?php if ($page->flavor_profile()->isNotEmpty()): ?>
                    <section class="ingredient-section">
                        <h2 class="ingredient-section__title">Perfil de Sabor</h2>
                        <div class="ingredient-section__content">
                            <?= $page->flavor_profile()->kt() ?>
                        </div>
                    </section>
                    <?php endif ?>

                    <!-- Culinary Uses -->
                    <?php if ($page->culinary_uses()->isNotEmpty()): ?>
                    <section class="ingredient-section">
                        <h2 class="ingredient-section__title"><?= t('ingredient.uses') ?></h2>
                        <div class="ingredient-section__content">
                            <?= $page->culinary_uses()->kt() ?>
                        </div>
                    </section>
                    <?php endif ?>

                    <!-- Storage -->
                    <?php if ($page->storage_tips()->isNotEmpty()): ?>
                    <section class="ingredient-section">
                        <h2 class="ingredient-section__title"><?= t('ingredient.storage') ?></h2>
                        <div class="ingredient-section__content">
                            <?= $page->storage_tips()->kt() ?>
                        </div>
                    </section>
                    <?php endif ?>

                    <!-- Origin/History -->
                    <?php if ($page->origin()->isNotEmpty()): ?>
                    <section class="ingredient-section">
                        <h2 class="ingredient-section__title">Origen e Historia</h2>
                        <div class="ingredient-section__content">
                            <?= $page->origin()->kt() ?>
                        </div>
                    </section>
                    <?php endif ?>
                </div>

                <!-- Sidebar -->
                <aside class="ingredient-page__sidebar">
                    <!-- Where to Buy -->
                    <?php $stores = $page->available_at()->toPages(); ?>
                    <?php if ($stores->isNotEmpty()): ?>
                    <div class="sidebar-box">
                        <h3 class="sidebar-box__title"><?= t('ingredient.where_to_buy') ?></h3>
                        <ul class="store-list">
                            <?php foreach ($stores as $store): ?>
                            <li class="store-list__item">
                                <a href="<?= $store->url() ?>">
                                    <?php if ($logo = $store->logo()->toFile()): ?>
                                        <img src="<?= $logo->thumb(['width' => 40])->url() ?>" alt="<?= $store->title() ?>">
                                    <?php endif ?>
                                    <span><?= $store->title() ?></span>
                                </a>
                            </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                    <?php endif ?>

                    <!-- Substitutes -->
                    <?php $substitutes = $page->substitutes()->toPages(); ?>
                    <?php if ($substitutes->isNotEmpty()): ?>
                    <div class="sidebar-box">
                        <h3 class="sidebar-box__title"><?= t('ingredient.substitutes') ?></h3>
                        <ul class="substitute-list">
                            <?php foreach ($substitutes as $sub): ?>
                            <li>
                                <a href="<?= $sub->url() ?>"><?= $sub->title() ?></a>
                            </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                    <?php endif ?>

                    <!-- Recipes with this ingredient -->
                    <?php
                    $recipesWithIngredient = page('recetas')->children()->listed()->filter(function($recipe) use ($page) {
                        foreach ($recipe->ingredients()->toStructure() as $ing) {
                            if ($ing->ingredient_link()->toPage() && $ing->ingredient_link()->toPage()->is($page)) {
                                return true;
                            }
                            if (stripos($ing->ingredient()->value(), $page->title()->value()) !== false) {
                                return true;
                            }
                        }
                        return false;
                    })->limit(5);
                    ?>
                    <?php if ($recipesWithIngredient->isNotEmpty()): ?>
                    <div class="sidebar-box">
                        <h3 class="sidebar-box__title"><?= t('ingredient.recipes_with') ?></h3>
                        <ul class="recipe-mini-list">
                            <?php foreach ($recipesWithIngredient as $recipe): ?>
                            <li>
                                <a href="<?= $recipe->url() ?>" class="recipe-mini">
                                    <?php if ($cover = $recipe->cover()->toFile()): ?>
                                        <img src="<?= $cover->thumb(['width' => 60, 'height' => 60, 'crop' => true])->url() ?>" alt="">
                                    <?php endif ?>
                                    <span><?= $recipe->title() ?></span>
                                </a>
                            </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                    <?php endif ?>
                </aside>
            </div>
        </div>
    </div>
</article>

<?php snippet('footer') ?>
