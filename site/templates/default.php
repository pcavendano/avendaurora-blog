<?php snippet('header') ?>

<!-- DEBUG: Template = <?= $page->template() ?>, Intended = <?= $page->intendedTemplate() ?>, Slug = <?= $page->slug() ?> -->
<!-- DEBUG: Root = <?= $page->root() ?> -->
<!-- DEBUG: Files in root = <?= implode(', ', array_map('basename', glob($page->root() . '/*.txt') ?: [])) ?> -->

<section class="page-header">
    <div class="container">
        <h1 class="page-header__title"><?= $page->title() ?></h1>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="content">
            <?= $page->text()->kt() ?>
        </div>
    </div>
</section>

<?php snippet('footer') ?>
