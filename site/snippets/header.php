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

    <!-- Favicon (chilpaya pepper) -->
    <link rel="icon" href="<?= url('assets/favicon.svg') ?>" type="image/svg+xml">
    <?php if ($site->favicon()->toFile()): ?>
    <link rel="icon" href="<?= $site->favicon()->toFile()->url() ?>" type="image/png">
    <?php endif ?>

    <!-- CSS (cache-busted) -->
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>?v=<?= filemtime(kirby()->root('assets') . '/css/style.css') ?>">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@500;700&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,700&family=Fraunces:ital,opsz,wght,SOFT@0,9..144,300..900,0..100;1,9..144,300..900,0..100&family=Rozha+One&display=swap" rel="stylesheet">
</head>
<body class="<?= $page->template() ?>">

<header class="header" id="siteHeader">
    <div class="header__container">
        <!-- Logo -->
        <a href="<?= $site->url() ?>" class="header__logo">
            <?php if ($site->logo()->toFile()): ?>
                <img src="<?= $site->logo()->toFile()->url() ?>" alt="<?= $site->title() ?>">
            <?php else: ?>
                <span class="header__logo-text">Aurora</span>
            <?php endif ?>
        </a>

        <!-- Navigation -->
        <nav class="header__nav" id="mainNav">
            <ul class="nav">
                <li class="nav__item">
                    <a href="<?= page('about')->url() ?>" class="nav__link <?= $page->is(page('about')) ? 'is-active' : '' ?>">
                        Aurora
                    </a>
                </li>
                <li class="nav__item">
                    <a href="<?= page('recetas')->url() ?>" class="nav__link <?= $page->is(page('recetas')) || $page->parent() && $page->parent()->is(page('recetas')) ? 'is-active' : '' ?>">
                        <?= t('nav.mi_cocina') ?>
                    </a>
                </li>
                <li class="nav__item">
                    <a href="<?= page('blog')->url() ?>" class="nav__link <?= $page->is(page('blog')) || $page->parent() && $page->parent()->is(page('blog')) ? 'is-active' : '' ?>">
                        Blog
                    </a>
                </li>
                <li class="nav__item">
                    <a href="<?= page('about')->url() ?>#contacto" class="nav__link">
                        <?= t('nav.contact') ?>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Header Actions -->
        <div class="header__actions">
            <!-- Language Switcher -->
            <?php snippet('language-switcher') ?>

            <!-- Search -->
            <?php $searchPage = page('buscar') ?>
            <form action="<?= $searchPage ? $searchPage->url() : url('buscar') ?>" method="get" class="header__search" id="headerSearch" role="search">
                <input type="search"
                       name="q"
                       class="header__search-input"
                       id="headerSearchInput"
                       placeholder="<?= t('general.search_placeholder') ?>"
                       aria-label="<?= t('nav.search') ?>">
                <button type="button" class="header__search-btn" id="headerSearchToggle" aria-label="<?= t('nav.search') ?>" aria-expanded="false">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                </button>
            </form>

            <!-- Account -->
            <?php if ($currentUser = $kirby->user()): ?>
                <div class="header__account" id="headerAccount">
                    <button type="button" class="header__account-btn" id="headerAccountToggle" aria-haspopup="true" aria-expanded="false">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="12" cy="8" r="4"></circle>
                            <path d="M4 21v-1a8 8 0 0 1 16 0v1"></path>
                        </svg>
                        <span class="header__account-name"><?= esc($currentUser->display_name()->or(strstr($currentUser->email(), '@', true))) ?></span>
                    </button>
                    <div class="header__account-menu">
                        <a href="<?= page('cuenta/perfil')->url() ?>"><?= t('account.my_profile') ?></a>
                        <?php if ($currentUser->role()->name() === 'admin'): ?>
                            <?php if ($importPage = page('importar-receta')): ?>
                                <a href="<?= $importPage->url() ?>">Importar Receta</a>
                            <?php endif ?>
                        <?php endif ?>
                        <a href="<?= url('cuenta/salir') ?>"><?= t('account.sign_out') ?></a>
                    </div>
                </div>
            <?php else: ?>
                <a href="<?= page('cuenta/iniciar-sesion')->url() ?>" class="header__account-link" aria-label="<?= t('account.sign_in') ?>">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <circle cx="12" cy="8" r="4"></circle>
                        <path d="M4 21v-1a8 8 0 0 1 16 0v1"></path>
                    </svg>
                </a>
            <?php endif ?>

        </div>

        <!-- Mobile Menu Toggle -->
        <button class="header__menu-toggle" id="menuToggle" aria-label="Menu">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</header>

<main class="main">