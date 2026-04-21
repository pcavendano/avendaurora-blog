<?php PageStats::record($page->id()); ?>
<?php snippet('header') ?>

<article class="about">

    <!-- Hero: Name + Full-width Photo (above the fold, GBC style) -->
    <section class="about-hero">
        <div class="about-hero__header">
            <h1 class="about-hero__title"><?= $page->title() ?></h1>
            <?php if ($page->subtitle()->isNotEmpty()): ?>
                <p class="about-hero__subtitle"><?= $page->subtitle() ?></p>
            <?php endif ?>
        </div>

        <div class="about-hero__photo">
            <?php if ($portrait = $page->portrait()->toFile()): ?>
                <?php snippet('responsive-image', [
                    'image' => $portrait,
                    'widths' => [800, 1024, 1440, 2048],
                    'sizes' => '100vw',
                    'alt' => $portrait->alt()->or($page->title())->value(),
                    'eager' => true,
                ]) ?>
            <?php else: ?>
                <div class="about-hero__photo-placeholder">
                    <svg width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="0.8">
                        <rect x="3" y="3" width="18" height="18" rx="2"/>
                        <circle cx="8.5" cy="8.5" r="1.5"/>
                        <path d="M21 15l-5-5L5 21"/>
                    </svg>
                    <span>Agregar foto horizontal del chef</span>
                </div>
            <?php endif ?>
        </div>
    </section>

    <!-- Intro + Social (centered italic intro, then social links) -->
    <section class="about-intro section">
        <div class="container container--narrow">
            <?php if ($page->intro()->isNotEmpty()): ?>
                <div class="about-intro__text">
                    <p><?= $page->intro() ?></p>
                </div>
            <?php endif ?>

            <!-- Social Media Links -->
            <?php if ($site->instagram()->isNotEmpty() || $site->facebook()->isNotEmpty()): ?>
            <div class="about-social">
                <span class="about-social__label"><?= t('contact.follow') ?></span>
                <?php if ($site->instagram()->isNotEmpty()): ?>
                    <a href="<?= $site->instagram() ?>" target="_blank" rel="noopener" class="about-social__link">
                        Instagram
                    </a>
                <?php endif ?>
                <?php if ($site->facebook()->isNotEmpty()): ?>
                    <a href="<?= $site->facebook() ?>" target="_blank" rel="noopener" class="about-social__link">
                        Facebook
                    </a>
                <?php endif ?>
                <?php if ($site->youtube()->isNotEmpty()): ?>
                    <a href="<?= $site->youtube() ?>" target="_blank" rel="noopener" class="about-social__link">
                        YouTube
                    </a>
                <?php endif ?>
            </div>
            <?php endif ?>
        </div>
    </section>

    <!-- Body: reorderable blocks (bio, quote, education, experience, dishes, gallery, headings, text) -->
    <?= $page->body()->toBlocks() ?>

    <!-- Specialties (sidebar field, always rendered at end) -->
    <?php if ($page->specialties()->isNotEmpty()): ?>
    <section class="about-specialties section">
        <div class="container">
            <h2 class="section__title"><?= $page->specialties_title()->or('Especialidades') ?></h2>
            <div class="about-specialties__grid">
                <?php foreach ($page->specialties()->yaml() as $specialty): ?>
                    <div class="about-specialties__item">
                        <span class="about-specialties__icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22,4 12,14.01 9,11.01"/>
                            </svg>
                        </span>
                        <?= $specialty ?>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </section>
    <?php endif ?>

    <!-- Notable Places -->
    <?php if ($page->notable_places()->isNotEmpty()): ?>
    <section class="about-notable section section--alt">
        <div class="container">
            <h2 class="section__title"><?= $page->notable_places_title()->or('Lugares Destacados') ?></h2>
            <p class="about-notable__list"><?= $page->notable_places() ?></p>
        </div>
    </section>
    <?php endif ?>

    <!-- Contact CTA -->
    <?php if ($page->email()->isNotEmpty()): ?>
    <section class="about-contact section section--alt" id="contacto">
        <div class="container container--narrow">
            <h2 class="section__title"><?= $page->contact_title()->or('Contacto') ?></h2>
            <?php if ($page->contact_intro()->isNotEmpty()): ?>
                <p class="about-contact__intro"><?= $page->contact_intro() ?></p>
            <?php endif ?>
            <a href="mailto:<?= $page->email() ?>" class="btn btn--primary btn--large">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                    <polyline points="22,6 12,13 2,6"/>
                </svg>
                <?= t('contact.email') ?>
            </a>
        </div>
    </section>
    <?php endif ?>

</article>

<?php snippet('footer') ?>
