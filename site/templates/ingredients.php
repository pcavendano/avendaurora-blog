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
        <?php if ($page->intro()->isNotEmpty()): ?>
            <div class="page-intro">
                <?= $page->intro()->kt() ?>
            </div>
        <?php endif ?>

        <!-- Category Filter -->
        <div class="ingredient-filters">
            <button class="filter-btn is-active" data-category="all"><?= t('filter.all') ?></button>
            <?php
            $categories = ['chiles', 'especias', 'hierbas', 'granos', 'lacteos', 'carnes', 'otros'];
            foreach ($categories as $cat):
            ?>
            <button class="filter-btn" data-category="<?= $cat ?>">
                <?= t('ingredient.category.' . $cat) ?>
            </button>
            <?php endforeach ?>
        </div>

        <!-- Ingredients Grid -->
        <div class="ingredient-grid">
            <?php
            $ingredients = $page->children()->listed()->sortBy('title', 'asc');
            foreach ($ingredients as $ingredient):
            ?>
            <article class="ingredient-card" data-category="<?= $ingredient->category() ?>">
                <a href="<?= $ingredient->url() ?>" class="ingredient-card__link">
                    <?php if ($cover = $ingredient->cover()->toFile()): ?>
                        <div class="ingredient-card__image">
                            <img src="<?= $cover->thumb(['width' => 300, 'height' => 300, 'crop' => true])->url() ?>"
                                 alt="<?= $ingredient->title() ?>"
                                 loading="lazy">
                        </div>
                    <?php else: ?>
                        <div class="ingredient-card__image ingredient-card__image--placeholder">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                                <path d="M2 17l10 5 10-5"/>
                                <path d="M2 12l10 5 10-5"/>
                            </svg>
                        </div>
                    <?php endif ?>

                    <div class="ingredient-card__content">
                        <h2 class="ingredient-card__title"><?= $ingredient->title() ?></h2>

                        <?php if ($ingredient->category()->value() === 'chiles' && $ingredient->heat_level()->isNotEmpty()): ?>
                            <div class="ingredient-card__heat">
                                <div class="heat-meter heat-meter--small">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="heat-meter__dot <?= $i <= ceil($ingredient->heat_level()->toInt() / 2) ? 'is-active' : '' ?>"></span>
                                    <?php endfor ?>
                                </div>
                            </div>
                        <?php endif ?>

                        <?php if ($ingredient->also_known_as()->isNotEmpty()): ?>
                            <p class="ingredient-card__aka"><?= Str::short($ingredient->also_known_as(), 50) ?></p>
                        <?php endif ?>
                    </div>
                </a>
            </article>
            <?php endforeach ?>
        </div>

        <?php if ($ingredients->isEmpty()): ?>
            <div class="empty-state">
                <p><?= t('general.no_results') ?></p>
            </div>
        <?php endif ?>
    </div>
</section>

<script>
// Client-side filtering
document.querySelectorAll('.ingredient-filters .filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const category = this.dataset.category;

        // Update active state
        document.querySelectorAll('.ingredient-filters .filter-btn').forEach(b => b.classList.remove('is-active'));
        this.classList.add('is-active');

        // Filter cards
        document.querySelectorAll('.ingredient-card').forEach(card => {
            if (category === 'all' || card.dataset.category === category) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
});
</script>

<?php snippet('footer') ?>
