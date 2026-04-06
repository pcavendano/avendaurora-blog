<?php snippet('header') ?>

<article class="article" itemscope itemtype="https://schema.org/BlogPosting">
    <!-- Breadcrumb -->
    <nav class="breadcrumb">
        <div class="container">
            <a href="<?= $site->url() ?>"><?= t('nav.home') ?></a>
            <span>/</span>
            <a href="<?= page('blog')->url() ?>">Blog</a>
            <span>/</span>
            <span><?= $page->title() ?></span>
        </div>
    </nav>

    <!-- Article Header -->
    <header class="article__header">
        <div class="container container--narrow">
            <?php
            $categories = [
                'ingredientes' => 'Ingredientes',
                'tecnicas' => 'Técnicas Culinarias',
                'cultura' => 'Cultura Mexicana',
                'consejos' => 'Consejos del Chef',
                'historias' => 'Historias y Tradiciones',
                'productos' => 'Productos y Reseñas'
            ];
            ?>
            <span class="article__category"><?= $categories[$page->category()->value()] ?? $page->category() ?></span>
            <h1 class="article__title" itemprop="headline"><?= $page->title() ?></h1>

            <?php if ($page->subtitle()->isNotEmpty()): ?>
                <p class="article__subtitle"><?= $page->subtitle() ?></p>
            <?php endif ?>

            <div class="article__meta">
                <span class="article__author" itemprop="author"><?= $page->author()->or('Aurora Avendano') ?></span>
                <span class="article__separator">•</span>
                <time class="article__date" itemprop="datePublished" datetime="<?= $page->date()->toDate('Y-m-d') ?>">
                    <?= $page->date()->toDate('d de F, Y') ?>
                </time>
                <?php if ($page->reading_time()->isNotEmpty()): ?>
                    <span class="article__separator">•</span>
                    <span class="article__reading-time"><?= $page->reading_time() ?> min de lectura</span>
                <?php endif ?>
            </div>
        </div>
    </header>

    <!-- Cover Image -->
    <?php if ($cover = $page->cover()->toFile()): ?>
    <div class="article__cover">
        <div class="container">
            <img src="<?= $cover->thumb(['width' => 1200])->url() ?>"
                 srcset="<?= $cover->srcset([600, 900, 1200, 1800]) ?>"
                 sizes="(max-width: 1200px) 100vw, 1200px"
                 alt="<?= $page->title() ?>"
                 itemprop="image"
                 class="article__cover-image">
        </div>
    </div>
    <?php endif ?>

    <!-- Article Content -->
    <div class="article__content">
        <div class="container container--narrow">
            <!-- Introduction -->
            <?php if ($page->intro()->isNotEmpty()): ?>
                <div class="article__intro" itemprop="description">
                    <?= $page->intro()->kt() ?>
                </div>
            <?php endif ?>

            <!-- Main Content (Blocks) -->
            <div class="article__body" itemprop="articleBody">
                <?php foreach ($page->content()->get('content')->toBlocks() as $block): ?>
                    <div class="block block-<?= $block->type() ?>">
                        <?= $block ?>
                    </div>
                <?php endforeach ?>
            </div>

            <!-- Tags -->
            <?php if ($page->tags()->isNotEmpty()): ?>
            <div class="article__tags">
                <?php foreach ($page->tags()->split(',') as $tag): ?>
                    <a href="<?= page('blog')->url() ?>?tag=<?= urlencode(trim($tag)) ?>" class="tag">
                        #<?= trim($tag) ?>
                    </a>
                <?php endforeach ?>
            </div>
            <?php endif ?>

            <!-- Share -->
            <div class="article__share">
                <span class="article__share-label">Compartir:</span>
                <div class="article__share-buttons">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($page->url()) ?>"
                       target="_blank" rel="noopener" class="share-btn share-btn--facebook">
                        Facebook
                    </a>
                    <a href="https://twitter.com/intent/tweet?url=<?= urlencode($page->url()) ?>&text=<?= urlencode($page->title()) ?>"
                       target="_blank" rel="noopener" class="share-btn share-btn--twitter">
                        Twitter
                    </a>
                    <a href="https://wa.me/?text=<?= urlencode($page->title() . ' ' . $page->url()) ?>"
                       target="_blank" rel="noopener" class="share-btn share-btn--whatsapp">
                        WhatsApp
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Recipes -->
    <?php if ($page->related_recipes()->isNotEmpty()): ?>
    <section class="article__related section section--alt">
        <div class="container">
            <h2 class="section__title">Recetas Relacionadas</h2>
            <div class="recipe-grid recipe-grid--small">
                <?php foreach ($page->related_recipes()->toPages() as $recipe): ?>
                    <?php snippet('recipe-card', ['recipe' => $recipe]) ?>
                <?php endforeach ?>
            </div>
        </div>
    </section>
    <?php endif ?>

    <!-- Related Ingredients -->
    <?php if ($page->related_ingredients()->isNotEmpty()): ?>
    <section class="article__ingredients section">
        <div class="container">
            <h2 class="section__title">Ingredientes Mencionados</h2>
            <div class="ingredients-grid">
                <?php foreach ($page->related_ingredients()->toPages() as $ingredient): ?>
                    <a href="<?= $ingredient->url() ?>" class="ingredient-card">
                        <?php if ($img = $ingredient->cover()->toFile()): ?>
                            <img src="<?= $img->thumb(['width' => 150, 'height' => 150, 'crop' => true])->url() ?>"
                                 alt="<?= $ingredient->title() ?>">
                        <?php endif ?>
                        <span><?= $ingredient->title() ?></span>
                    </a>
                <?php endforeach ?>
            </div>
        </div>
    </section>
    <?php endif ?>

    <!-- Author Box -->
    <section class="article__author-box">
        <div class="container container--narrow">
            <div class="author-box">
                <?php if ($aboutPage = page('about')): ?>
                    <?php if ($portrait = $aboutPage->portrait()->toFile()): ?>
                        <img src="<?= $portrait->thumb(['width' => 100, 'height' => 100, 'crop' => true])->url() ?>"
                             alt="<?= $page->author() ?>"
                             class="author-box__image">
                    <?php endif ?>
                <?php endif ?>
                <div class="author-box__content">
                    <span class="author-box__label">Escrito por</span>
                    <h3 class="author-box__name"><?= $page->author()->or('Aurora Avendano') ?></h3>
                    <p class="author-box__bio">Chef profesional con más de 15 años de experiencia en la cocina mexicana.</p>
                    <a href="<?= page('about')->url() ?>" class="author-box__link">Conocer más →</a>
                </div>
            </div>
        </div>
    </section>

    <!-- More Articles -->
    <?php
    $moreArticles = $page->siblings()->listed()
        ->not($page)
        ->sortBy('date', 'desc')
        ->limit(3);
    ?>
    <?php if ($moreArticles->isNotEmpty()): ?>
    <section class="article__more section section--alt">
        <div class="container">
            <h2 class="section__title">Más Artículos</h2>
            <div class="blog-grid">
                <?php foreach ($moreArticles as $article): ?>
                <article class="article-card">
                    <a href="<?= $article->url() ?>" class="article-card__link">
                        <div class="article-card__image">
                            <?php if ($cover = $article->cover()->toFile()): ?>
                                <img src="<?= $cover->thumb(['width' => 600, 'height' => 400, 'crop' => true])->url() ?>"
                                     alt="<?= $article->title() ?>">
                            <?php endif ?>
                        </div>
                        <div class="article-card__content">
                            <h3 class="article-card__title"><?= $article->title() ?></h3>
                            <span class="article-card__date"><?= $article->date()->toDate('d M Y') ?></span>
                        </div>
                    </a>
                </article>
                <?php endforeach ?>
            </div>
        </div>
    </section>
    <?php endif ?>
</article>

<?php snippet('footer') ?>
