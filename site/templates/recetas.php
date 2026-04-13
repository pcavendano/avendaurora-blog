<?php snippet('header') ?>

<section class="page-header">
    <div class="container">
        <h1 class="page-header__title"><?= $page->title() ?></h1>
        <?php if ($page->description()->isNotEmpty()): ?>
            <p class="page-header__description"><?= $page->description() ?></p>
        <?php endif ?>
    </div>
</section>

<?php if ($cover = $page->cover()->toFile()): ?>
<div class="article__cover">
    <div class="container">
        <img src="<?= $cover->thumb(['width' => 1200])->url() ?>"
             srcset="<?= $cover->srcset([600, 900, 1200, 1800]) ?>"
             sizes="(max-width: 1200px) 100vw, 1200px"
             alt="<?= $page->title() ?>"
             class="article__cover-image">
    </div>
</div>
<?php endif ?>

<section class="section">
    <div class="container">
        <!-- Category Filter (dynamic: only categories with listed recipes) -->
        <?php
        $listedRecipes = $page->children()->listed();
        $categoryCounts = [];
        foreach ($listedRecipes as $recipe) {
            foreach ($recipe->category()->split(',') as $cat) {
                $cat = trim($cat);
                if ($cat === '') continue;
                $categoryCounts[$cat] = ($categoryCounts[$cat] ?? 0) + 1;
            }
        }
        ksort($categoryCounts);
        ?>
        <?php if (!empty($categoryCounts)): ?>
        <div class="recipe-filters">
            <button class="filter-btn is-active" data-category="all">
                Todas (<?= $listedRecipes->count() ?>)
            </button>
            <?php foreach ($categoryCounts as $cat => $count): ?>
            <button class="filter-btn" data-category="<?= esc($cat) ?>">
                <?= t('category.' . $cat, $cat) ?> (<?= $count ?>)
            </button>
            <?php endforeach ?>
        </div>
        <?php endif ?>

        <!-- Recipe Grid -->
        <div class="recipe-grid" id="recipe-grid">
            <?php
            // Paginate - 25 recipes per page
            $recipes = $page->children()->listed()->sortBy('title', 'asc')->paginate(25);

            foreach ($recipes as $recipe):
                snippet('recipe-card', ['recipe' => $recipe]);
            endforeach;
            ?>
        </div>

        <!-- Pagination -->
        <?php if ($recipes->pagination()->hasPages()): ?>
        <nav class="pagination">
            <?php if ($recipes->pagination()->hasPrevPage()): ?>
                <a href="<?= $recipes->pagination()->prevPageUrl() ?>" class="pagination__prev">&larr; Anterior</a>
            <?php endif ?>

            <span class="pagination__info">
                Página <?= $recipes->pagination()->page() ?> de <?= $recipes->pagination()->pages() ?>
            </span>

            <?php if ($recipes->pagination()->hasNextPage()): ?>
                <a href="<?= $recipes->pagination()->nextPageUrl() ?>" class="pagination__next">Siguiente &rarr;</a>
            <?php endif ?>
        </nav>
        <?php endif ?>

        <?php if ($recipes->isEmpty()): ?>
            <div class="empty-state">
                <p><?= t('general.no_results') ?></p>
            </div>
        <?php endif ?>
    </div>
</section>

<script>
// Simple client-side filtering (supports multiple categories per recipe)
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const category = this.dataset.category;

        // Update active state
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('is-active'));
        this.classList.add('is-active');

        // Filter cards by data-category attribute (supports comma-separated categories)
        document.querySelectorAll('.recipe-card').forEach(card => {
            if (category === 'all') {
                card.style.display = '';
            } else {
                const cardCategories = card.dataset.category.split(',').map(c => c.trim());
                card.style.display = cardCategories.includes(category) ? '' : 'none';
            }
        });
    });
});
</script>

<?php snippet('footer') ?>
