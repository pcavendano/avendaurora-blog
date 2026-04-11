<?php snippet('header') ?>

<?php
$query = trim((string) get('q', ''));
$recipeResults = $articleResults = $ingredientResults = null;
$totalResults = 0;

if ($query !== '') {
    $recipesParent = page('recetas');
    if ($recipesParent) {
        $recipeResults = $recipesParent->children()->search($query, 'title|description|tags|category');
        $totalResults += $recipeResults->count();
    }

    $articlesParent = page('blog');
    if ($articlesParent) {
        $articleResults = $articlesParent->children()->listed()->search($query, 'title|description|tags|intro');
        $totalResults += $articleResults->count();
    }

    $ingredientsParent = page('ingredientes');
    if ($ingredientsParent) {
        $ingredientResults = $ingredientsParent->children()->search($query, 'title|description|category');
        $totalResults += $ingredientResults->count();
    }
}
?>

<section class="page-header">
    <div class="container">
        <h1 class="page-header__title"><?= $page->title() ?></h1>

        <form action="<?= $page->url() ?>" method="get" class="search-page__form">
            <input type="search"
                   name="q"
                   value="<?= esc($query) ?>"
                   placeholder="<?= t('general.search_placeholder') ?>"
                   class="search-page__input"
                   autofocus>
            <button type="submit" class="search-page__submit"><?= t('nav.search') ?></button>
        </form>

        <?php if ($query !== ''): ?>
            <p class="search-page__summary">
                <?= $totalResults ?> <?= $totalResults === 1 ? t('search.result') : t('search.results') ?>
                <?= t('search.for') ?> <strong>"<?= esc($query) ?>"</strong>
            </p>
        <?php endif ?>
    </div>
</section>

<?php if ($query !== ''): ?>
<section class="section">
    <div class="container">

        <?php if ($totalResults === 0): ?>
            <div class="empty-state">
                <p><?= t('general.no_results') ?></p>
            </div>
        <?php endif ?>

        <?php if ($recipeResults && $recipeResults->count() > 0): ?>
            <h2 class="search-page__section-title"><?= t('nav.recipes') ?> (<?= $recipeResults->count() ?>)</h2>
            <div class="recipe-grid">
                <?php foreach ($recipeResults as $recipe): ?>
                    <?php snippet('recipe-card', ['recipe' => $recipe]) ?>
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <?php if ($ingredientResults && $ingredientResults->count() > 0): ?>
            <h2 class="search-page__section-title"><?= t('nav.ingredients') ?> (<?= $ingredientResults->count() ?>)</h2>
            <div class="ingredient-grid">
                <?php foreach ($ingredientResults as $ingredient): ?>
                    <?php snippet('ingredient-card', ['ingredient' => $ingredient]) ?>
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <?php if ($articleResults && $articleResults->count() > 0): ?>
            <h2 class="search-page__section-title">Blog (<?= $articleResults->count() ?>)</h2>
            <div class="article-grid">
                <?php foreach ($articleResults as $article): ?>
                    <article class="article-card">
                        <a href="<?= $article->url() ?>" class="article-card__link">
                            <?php if ($cover = $article->cover()->toFile()): ?>
                                <div class="article-card__image">
                                    <img src="<?= $cover->thumb(['width' => 600, 'height' => 400, 'crop' => true])->url() ?>"
                                         alt="<?= $article->title() ?>" loading="lazy">
                                </div>
                            <?php endif ?>
                            <div class="article-card__content">
                                <h3 class="article-card__title"><?= $article->title() ?></h3>
                                <?php if ($article->description()->isNotEmpty()): ?>
                                    <p class="article-card__description"><?= $article->description()->excerpt(120) ?></p>
                                <?php endif ?>
                            </div>
                        </a>
                    </article>
                <?php endforeach ?>
            </div>
        <?php endif ?>

    </div>
</section>
<?php endif ?>

<?php snippet('footer') ?>
