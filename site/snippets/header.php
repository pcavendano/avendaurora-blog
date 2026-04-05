<!DOCTYPE html>
<html lang="<?= $kirby->language()->code() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title><?= $page->seo_title()->or($page->title() . ' | ' . $site->title()) ?></title>
    <meta name="description" content="<?= $page->seo_description()->or($page->description())->or($site->default_seo_description()) ?>">

    <!-- Open Graph -->
    <meta property="og:title" content="<?= $page->title() ?>">
    <meta property="og:description" content="<?= $page->description() ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= $page->url() ?>">
    <?php if ($page->cover()->toFile()): ?>
    <meta property="og:image" content="<?= $page->cover()->toFile()->url() ?>">
    <?php endif ?>

    <!-- Favicon -->
    <?php if ($site->favicon()->toFile()): ?>
    <link rel="icon" href="<?= $site->favicon()->toFile()->url() ?>">
    <?php endif ?>

    <!-- CSS -->
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body class="<?= $page->template() ?>">

<header class="header">
    <div class="header__container">
        <!-- Logo -->
        <a href="<?= $site->url() ?>" class="header__logo">
            <?php if ($site->logo()->toFile()): ?>
                <img src="<?= $site->logo()->toFile()->url() ?>" alt="<?= $site->title() ?>">
            <?php else: ?>
                <span class="header__logo-text"><?= $site->title() ?></span>
            <?php endif ?>
        </a>

        <!-- Navigation -->
        <nav class="header__nav">
            <ul class="nav">
                <li class="nav__item">
                    <a href="<?= page('recetas')->url() ?>" class="nav__link <?= $page->is(page('recetas')) || $page->parent() && $page->parent()->is(page('recetas')) ? 'is-active' : '' ?>">
                        <?= t('nav.recipes') ?>
                    </a>
                </li>
                <li class="nav__item">
                    <a href="<?= page('ingredientes')->url() ?>" class="nav__link <?= $page->is(page('ingredientes')) ? 'is-active' : '' ?>">
                        <?= t('nav.ingredients') ?>
                    </a>
                </li>
                <li class="nav__item">
                    <a href="<?= page('blog')->url() ?>" class="nav__link <?= $page->is(page('blog')) || $page->parent() && $page->parent()->is(page('blog')) ? 'is-active' : '' ?>">
                        Blog
                    </a>
                </li>
                <li class="nav__item">
                    <a href="<?= page('about')->url() ?>" class="nav__link <?= $page->is(page('about')) ? 'is-active' : '' ?>">
                        <?= t('nav.about') ?>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Header Actions -->
        <div class="header__actions">
            <!-- Language Switcher -->
            <?php snippet('language-switcher') ?>

            <!-- Search -->
            <button class="header__search-btn" aria-label="<?= t('nav.search') ?>">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="m21 21-4.35-4.35"></path>
                </svg>
            </button>

        </div>

        <!-- Mobile Menu Toggle -->
        <button class="header__menu-toggle" aria-label="Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</header>

<main class="main">
