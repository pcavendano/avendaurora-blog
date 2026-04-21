<?php PageStats::record($page->id()); ?>
<?php snippet('header') ?>

<article class="contact">

    <section class="about-hero">
        <div class="about-hero__header">
            <h1 class="about-hero__title"><?= $page->title() ?></h1>
            <?php if ($page->intro()->isNotEmpty()): ?>
                <p class="about-hero__subtitle"><?= $page->intro() ?></p>
            <?php endif ?>
        </div>
    </section>

    <section class="about-contact section section--alt" id="contacto">
        <div class="container container--narrow">

            <?php if ($page->email()->isNotEmpty()): ?>
                <p class="about-contact__intro"><?= t('contact.email') ?></p>
                <a href="mailto:<?= $page->email() ?>" class="btn btn--primary btn--large">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                        <polyline points="22,6 12,13 2,6"/>
                    </svg>
                    <?= $page->email() ?>
                </a>
            <?php endif ?>

            <?php if ($page->phone()->isNotEmpty()): ?>
                <p class="about-contact__intro" style="margin-top: 2rem;"><?= t('contact.phone') ?>: <a href="tel:<?= $page->phone() ?>"><?= $page->phone() ?></a></p>
            <?php endif ?>

            <?php if ($page->location()->isNotEmpty()): ?>
                <p class="about-contact__intro"><?= t('contact.location') ?>: <?= $page->location() ?></p>
            <?php endif ?>

            <?php if ($page->body()->isNotEmpty()): ?>
                <div class="about-contact__intro" style="margin-top: 2rem;">
                    <?= $page->body()->kt() ?>
                </div>
            <?php endif ?>

            <?php if ($site->instagram()->isNotEmpty() || $site->facebook()->isNotEmpty() || $site->youtube()->isNotEmpty()): ?>
                <div class="about-social" style="margin-top: 2rem;">
                    <span class="about-social__label"><?= t('contact.follow') ?></span>
                    <?php if ($site->instagram()->isNotEmpty()): ?>
                        <a href="<?= $site->instagram() ?>" target="_blank" rel="noopener" class="about-social__link">Instagram</a>
                    <?php endif ?>
                    <?php if ($site->facebook()->isNotEmpty()): ?>
                        <a href="<?= $site->facebook() ?>" target="_blank" rel="noopener" class="about-social__link">Facebook</a>
                    <?php endif ?>
                    <?php if ($site->youtube()->isNotEmpty()): ?>
                        <a href="<?= $site->youtube() ?>" target="_blank" rel="noopener" class="about-social__link">YouTube</a>
                    <?php endif ?>
                </div>
            <?php endif ?>

        </div>
    </section>

</article>

<?php snippet('footer') ?>