<?php snippet('header') ?>

<section class="page-header">
    <div class="container">
        <h1 class="page-header__title"><?= $page->title() ?></h1>
        <?php if ($page->description()->isNotEmpty()): ?>
            <p class="page-header__description"><?= $page->description() ?></p>
        <?php endif ?>
    </div>
</section>

<section class="section">
    <div class="container">
        <!-- Category Filter -->
        <div class="recipe-filters">
            <button class="filter-btn is-active" data-category="all">Todas</button>
            <?php
            $categories = [
                'antojitos', 'platos-fuertes', 'sopas-caldos', 'salsas',
                'mariscos', 'desayunos', 'postres', 'bebidas', 'vegetarianos'
            ];
            foreach ($categories as $cat):
            ?>
            <button class="filter-btn" data-category="<?= $cat ?>">
                <?= t('category.' . $cat) ?>
            </button>
            <?php endforeach ?>
        </div>

        <!-- Debug: Show child count -->
        <p style="background: #ffeb3b; padding: 10px;">Debug: Found <?= $page->children()->count() ?> child pages</p>

        <!-- Recipe Grid -->
        <div class="recipe-grid" id="recipe-grid">
            <?php
            // Paginate - 25 recipes per page
            $recipes = $page->children()->sortBy('title', 'asc')->paginate(25);

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
