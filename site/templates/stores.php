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

        <!-- Stores Grid -->
        <div class="store-grid">
            <?php
            $stores = $page->children()->listed()->sortBy('title', 'asc');
            foreach ($stores as $store):
            ?>
            <article class="store-card">
                <a href="<?= $store->url() ?>" class="store-card__link">
                    <?php if ($logo = $store->logo()->toFile()): ?>
                        <div class="store-card__logo">
                            <img src="<?= $logo->thumb(['width' => 200])->url() ?>"
                                 alt="<?= $store->title() ?>">
                        </div>
                    <?php else: ?>
                        <div class="store-card__logo store-card__logo--placeholder">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                <polyline points="9 22 9 12 15 12 15 22"/>
                            </svg>
                        </div>
                    <?php endif ?>

                    <div class="store-card__content">
                        <h2 class="store-card__title"><?= $store->title() ?></h2>

                        <?php if ($store->address()->isNotEmpty()): ?>
                            <p class="store-card__address">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                <?= $store->address() ?>
                            </p>
                        <?php endif ?>

                        <?php if ($store->delivery()->toBool()): ?>
                            <span class="store-card__badge">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="1" y="3" width="15" height="13"/>
                                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                                    <circle cx="5.5" cy="18.5" r="2.5"/>
                                    <circle cx="18.5" cy="18.5" r="2.5"/>
                                </svg>
                                <?= t('store.delivery') ?>
                            </span>
                        <?php endif ?>

                        <?php if ($store->specialties()->isNotEmpty()): ?>
                            <div class="store-card__specialties">
                                <?php foreach (array_slice($store->specialties()->split(), 0, 3) as $specialty): ?>
                                    <span class="store-card__specialty"><?= $specialty ?></span>
                                <?php endforeach ?>
                            </div>
                        <?php endif ?>
                    </div>
                </a>
            </article>
            <?php endforeach ?>
        </div>

        <?php if ($stores->isEmpty()): ?>
            <div class="empty-state">
                <p><?= t('general.no_stores') ?></p>
            </div>
        <?php endif ?>
    </div>
</section>

<?php snippet('footer') ?>
