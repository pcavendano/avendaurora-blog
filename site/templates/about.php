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
                <img src="<?= $portrait->thumb(['width' => 1440])->url() ?>"
                     srcset="<?= $portrait->srcset([800, 1024, 1440, 2048]) ?>"
                     sizes="100vw"
                     alt="<?= $page->title() ?>">
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
                <span class="about-social__label">Redes Sociales</span>
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

    <!-- Biography (long-form text, left-aligned, readable width) -->
    <?php if ($page->bio()->isNotEmpty()): ?>
    <section class="about-bio section">
        <div class="container container--narrow">
            <div class="about-bio__content">
                <?= $page->bio()->kt() ?>
            </div>
            <?php if ($page->quote()->isNotEmpty()): ?>
                <blockquote class="about-bio__quote">
                    <p>"<?= $page->quote() ?>"</p>
                </blockquote>
            <?php endif ?>
        </div>
    </section>
    <?php endif ?>

    <!-- Signature Dishes (2 images side by side) -->
    <?php if ($page->signature_dishes()->isNotEmpty()): ?>
    <section class="about-dishes section section--alt">
        <div class="container">
            <h2 class="section__title text-center"><?= $page->signature_dishes_title()->or('Mis Platos Insignia') ?></h2>
            <div class="about-dishes__grid">
                <?php foreach ($page->signature_dishes()->toStructure() as $dish): ?>
                <div class="about-dishes__item">
                    <?php if ($img = $dish->image()->toFile()): ?>
                    <div class="about-dishes__image">
                        <img src="<?= $img->thumb(['width' => 700, 'height' => 500, 'crop' => true])->url() ?>"
                             srcset="<?= $img->srcset([400, 600, 700, 1000]) ?>"
                             sizes="(max-width: 768px) 100vw, 50vw"
                             alt="<?= $dish->title() ?>"
                             loading="lazy">
                    </div>
                    <?php endif ?>
                    <div class="about-dishes__content">
                        <h3 class="about-dishes__title"><?= $dish->title() ?></h3>
                        <?php if ($dish->description()->isNotEmpty()): ?>
                            <p class="about-dishes__description"><?= $dish->description() ?></p>
                        <?php endif ?>
                    </div>
                </div>
                <?php endforeach ?>
            </div>
        </div>
    </section>
    <?php endif ?>

    <!-- Specialties -->
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

    <!-- Education -->
    <?php if ($page->education()->isNotEmpty()): ?>
    <section class="about-education section section--alt">
        <div class="container">
            <h2 class="section__title"><?= $page->education_title()->or('Formacion') ?></h2>
            <div class="about-timeline">
                <?php foreach ($page->education()->toStructure() as $edu): ?>
                    <div class="about-timeline__item">
                        <div class="about-timeline__year"><?= $edu->year() ?></div>
                        <div class="about-timeline__content">
                            <h3 class="about-timeline__title"><?= $edu->title() ?></h3>
                            <p class="about-timeline__institution"><?= $edu->institution() ?></p>
                            <?php if ($edu->description()->isNotEmpty()): ?>
                                <p class="about-timeline__description"><?= $edu->description() ?></p>
                            <?php endif ?>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    </section>
    <?php endif ?>

    <!-- Experience -->
    <?php if ($page->experience()->isNotEmpty()): ?>
    <section class="about-experience section">
        <div class="container">
            <h2 class="section__title"><?= $page->experience_title()->or('Experiencia') ?></h2>
            <div class="about-timeline">
                <?php foreach ($page->experience()->toStructure() as $exp): ?>
                    <div class="about-timeline__item">
                        <div class="about-timeline__year"><?= $exp->period() ?></div>
                        <div class="about-timeline__content">
                            <h3 class="about-timeline__title"><?= $exp->position() ?></h3>
                            <p class="about-timeline__institution"><?= $exp->place() ?></p>
                            <?php if ($exp->description()->isNotEmpty()): ?>
                                <p class="about-timeline__description"><?= $exp->description() ?></p>
                            <?php endif ?>
                        </div>
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

    <!-- Philosophy -->
    <?php if ($page->philosophy()->isNotEmpty()): ?>
    <section class="about-philosophy section">
        <div class="container container--narrow">
            <h2 class="section__title"><?= $page->philosophy_title()->or('Mi Filosofia') ?></h2>
            <div class="about-philosophy__content">
                <?= $page->philosophy()->kt() ?>
            </div>
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
                Enviar Email
            </a>
        </div>
    </section>
    <?php endif ?>

</article>

<?php snippet('footer') ?>
