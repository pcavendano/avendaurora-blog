<?php snippet('header') ?>

<article class="store-page">
    <!-- Breadcrumb -->
    <nav class="breadcrumb">
        <div class="container">
            <a href="<?= $site->url() ?>"><?= t('nav.home') ?></a>
            <span>/</span>
            <a href="<?= page('tiendas')->url() ?>"><?= t('nav.stores') ?></a>
            <span>/</span>
            <span><?= $page->title() ?></span>
        </div>
    </nav>

    <!-- Header -->
    <header class="store-page__header">
        <div class="container">
            <div class="store-page__header-grid">
                <?php if ($logo = $page->logo()->toFile()): ?>
                <div class="store-page__logo">
                    <img src="<?= $logo->thumb(['width' => 200])->url() ?>" alt="<?= $page->title() ?>">
                </div>
                <?php endif ?>

                <div class="store-page__info">
                    <h1 class="store-page__title"><?= $page->title() ?></h1>

                    <?php if ($page->delivery()->toBool()): ?>
                        <span class="store-page__badge">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="1" y="3" width="15" height="13"/>
                                <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                                <circle cx="5.5" cy="18.5" r="2.5"/>
                                <circle cx="18.5" cy="18.5" r="2.5"/>
                            </svg>
                            <?= t('store.delivery_available') ?>
                        </span>
                        <?php if ($page->delivery_info()->isNotEmpty()): ?>
                            <p class="store-page__delivery-info"><?= $page->delivery_info() ?></p>
                        <?php endif ?>
                    <?php endif ?>
                </div>
            </div>
        </div>
    </header>

    <div class="store-page__content">
        <div class="container">
            <div class="store-page__grid">
                <!-- Main Content -->
                <div class="store-page__main">
                    <?php if ($page->description()->isNotEmpty()): ?>
                    <section class="store-section">
                        <h2 class="store-section__title"><?= t('store.about') ?></h2>
                        <div class="store-section__content">
                            <?= $page->description()->kt() ?>
                        </div>
                    </section>
                    <?php endif ?>

                    <?php if ($page->specialties()->isNotEmpty()): ?>
                    <section class="store-section">
                        <h2 class="store-section__title"><?= t('store.specialties') ?></h2>
                        <div class="specialty-tags">
                            <?php foreach ($page->specialties()->split() as $specialty): ?>
                                <span class="specialty-tag"><?= $specialty ?></span>
                            <?php endforeach ?>
                        </div>
                    </section>
                    <?php endif ?>

                    <!-- Ingredients available at this store -->
                    <?php
                    $ingredientsHere = page('ingredientes')->children()->listed()->filter(function($ing) use ($page) {
                        return $ing->available_at()->toPages()->has($page);
                    });
                    ?>
                    <?php if ($ingredientsHere->isNotEmpty()): ?>
                    <section class="store-section">
                        <h2 class="store-section__title"><?= t('store.ingredients_available') ?></h2>
                        <div class="ingredient-mini-grid">
                            <?php foreach ($ingredientsHere as $ing): ?>
                            <a href="<?= $ing->url() ?>" class="ingredient-mini">
                                <?php if ($cover = $ing->cover()->toFile()): ?>
                                    <img src="<?= $cover->thumb(['width' => 60, 'height' => 60, 'crop' => true])->url() ?>" alt="">
                                <?php endif ?>
                                <span><?= $ing->title() ?></span>
                            </a>
                            <?php endforeach ?>
                        </div>
                    </section>
                    <?php endif ?>
                </div>

                <!-- Sidebar -->
                <aside class="store-page__sidebar">
                    <div class="sidebar-box">
                        <h3 class="sidebar-box__title"><?= t('store.contact') ?></h3>

                        <?php if ($page->address()->isNotEmpty()): ?>
                        <div class="store-contact__item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                                <circle cx="12" cy="10" r="3"/>
                            </svg>
                            <span><?= $page->address() ?></span>
                        </div>
                        <?php endif ?>

                        <?php if ($page->phone()->isNotEmpty()): ?>
                        <div class="store-contact__item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/>
                            </svg>
                            <a href="tel:<?= $page->phone() ?>"><?= $page->phone() ?></a>
                        </div>
                        <?php endif ?>

                        <?php if ($page->website()->isNotEmpty()): ?>
                        <div class="store-contact__item">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="2" y1="12" x2="22" y2="12"/>
                                <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>
                            </svg>
                            <a href="<?= $page->website() ?>" target="_blank" rel="noopener"><?= t('store.visit_website') ?></a>
                        </div>
                        <?php endif ?>
                    </div>

                    <?php if ($page->hours()->isNotEmpty()): ?>
                    <div class="sidebar-box">
                        <h3 class="sidebar-box__title"><?= t('store.hours') ?></h3>
                        <ul class="store-hours">
                            <?php foreach ($page->hours()->toStructure() as $schedule): ?>
                            <li class="store-hours__item">
                                <span class="store-hours__day"><?= $schedule->day() ?></span>
                                <span class="store-hours__time"><?= $schedule->hours() ?></span>
                            </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                    <?php endif ?>

                    <?php if ($page->affiliate_link()->isNotEmpty()): ?>
                    <a href="<?= $page->affiliate_link() ?>" class="btn btn--primary btn--block" target="_blank" rel="noopener">
                        <?= t('store.shop_online') ?>
                    </a>
                    <?php endif ?>
                </aside>
            </div>
        </div>
    </div>
</article>

<?php snippet('footer') ?>
