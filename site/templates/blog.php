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
        <div class="blog-filters">
            <button class="filter-btn is-active" data-category="all">Todos</button>
            <?php
            $categories = [
                'ingredientes' => 'Ingredientes',
                'tecnicas' => 'Técnicas',
                'cultura' => 'Cultura',
                'consejos' => 'Consejos',
                'historias' => 'Historias',
                'productos' => 'Productos'
            ];
            foreach ($categories as $slug => $name):
            ?>
            <button class="filter-btn" data-category="<?= $slug ?>"><?= $name ?></button>
            <?php endforeach ?>
        </div>

        <!-- Featured Article -->
        <?php $featured = $page->children()->listed()->filterBy('featured', true)->first(); ?>
        <?php if ($featured): ?>
        <article class="featured-article" data-category="<?= $featured->category() ?>">
            <a href="<?= $featured->url() ?>" class="featured-article__link">
                <div class="featured-article__image">
                    <?php if ($cover = $featured->cover()->toFile()): ?>
                        <img src="<?= $cover->thumb(['width' => 1200, 'height' => 600, 'crop' => true])->url() ?>"
                             alt="<?= $featured->title() ?>">
                    <?php endif ?>
                    <span class="featured-article__badge">Destacado</span>
                </div>
                <div class="featured-article__content">
                    <span class="featured-article__category"><?= $categories[$featured->category()->value()] ?? $featured->category() ?></span>
                    <h2 class="featured-article__title"><?= $featured->title() ?></h2>
                    <?php if ($featured->intro()->isNotEmpty()): ?>
                        <p class="featured-article__intro"><?= $featured->intro()->excerpt(200) ?></p>
                    <?php endif ?>
                    <div class="featured-article__meta">
                        <span class="featured-article__date"><?= $featured->date()->toDate('d M Y') ?></span>
                        <?php if ($featured->reading_time()->isNotEmpty()): ?>
                            <span class="featured-article__reading-time"><?= $featured->reading_time() ?> min de lectura</span>
                        <?php endif ?>
                    </div>
                </div>
            </a>
        </article>
        <?php endif ?>

        <!-- Articles Grid -->
        <div class="blog-grid">
            <?php
            $articles = $page->children()->listed()->sortBy('date', 'desc');
            if ($featured) {
                $articles = $articles->not($featured);
            }

            foreach ($articles as $article):
            ?>
            <article class="article-card" data-category="<?= $article->category() ?>">
                <a href="<?= $article->url() ?>" class="article-card__link">
                    <div class="article-card__image">
                        <?php if ($cover = $article->cover()->toFile()): ?>
                            <img src="<?= $cover->thumb(['width' => 600, 'height' => 400, 'crop' => true])->url() ?>"
                                 alt="<?= $article->title() ?>"
                                 loading="lazy">
                        <?php else: ?>
                            <div class="article-card__placeholder">
                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                    <polyline points="14,2 14,8 20,8"/>
                                    <line x1="16" y1="13" x2="8" y2="13"/>
                                    <line x1="16" y1="17" x2="8" y2="17"/>
                                    <polyline points="10,9 9,9 8,9"/>
                                </svg>
                            </div>
                        <?php endif ?>
                        <span class="article-card__category"><?= $categories[$article->category()->value()] ?? $article->category() ?></span>
                    </div>
                    <div class="article-card__content">
                        <h3 class="article-card__title"><?= $article->title() ?></h3>
                        <?php if ($article->intro()->isNotEmpty()): ?>
                            <p class="article-card__intro"><?= $article->intro()->excerpt(120) ?></p>
                        <?php endif ?>
                        <div class="article-card__meta">
                            <span class="article-card__date"><?= $article->date()->toDate('d M Y') ?></span>
                            <?php if ($article->reading_time()->isNotEmpty()): ?>
                                <span class="article-card__reading-time"><?= $article->reading_time() ?> min</span>
                            <?php endif ?>
                        </div>
                    </div>
                </a>
            </article>
            <?php endforeach ?>
        </div>

        <?php if ($articles->isEmpty() && !$featured): ?>
            <div class="empty-state">
                <p>No hay artículos publicados todavía.</p>
            </div>
        <?php endif ?>
    </div>
</section>

<script>
// Blog filtering
document.querySelectorAll('.blog-filters .filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const category = this.dataset.category;

        document.querySelectorAll('.blog-filters .filter-btn').forEach(b => b.classList.remove('is-active'));
        this.classList.add('is-active');

        document.querySelectorAll('.article-card, .featured-article').forEach(card => {
            if (category === 'all') {
                card.style.display = '';
            } else {
                card.style.display = card.dataset.category === category ? '' : 'none';
            }
        });
    });
});
</script>

<?php snippet('footer') ?>
