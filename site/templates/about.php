<?php snippet('header') ?>

<section class="about-hero">
    <div class="container">
        <div class="about-hero__grid">
            <!-- Portrait -->
            <div class="about-hero__portrait">
                <?php if ($portrait = $page->portrait()->toFile()): ?>
                    <img src="<?= $portrait->thumb(['width' => 500, 'height' => 600, 'crop' => true])->url() ?>"
                         alt="<?= $page->title() ?>"
                         class="about-hero__image">
                <?php else: ?>
                    <!-- Portrait Placeholder -->
                    <div class="about-hero__placeholder">
                        <svg width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                        <span>Agregar foto de perfil</span>
                    </div>
                <?php endif ?>
            </div>

            <!-- Intro Content -->
            <div class="about-hero__content">
                <h1 class="about-hero__title"><?= $page->title() ?></h1>
                <?php if ($page->subtitle()->isNotEmpty()): ?>
                    <p class="about-hero__subtitle"><?= $page->subtitle() ?></p>
                <?php endif ?>
                <?php if ($page->intro()->isNotEmpty()): ?>
                    <p class="about-hero__intro"><?= $page->intro() ?></p>
                <?php endif ?>
            </div>
        </div>
    </div>
</section>

<!-- Biography -->
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

<!-- Specialties -->
<?php if ($page->specialties()->isNotEmpty()): ?>
<section class="about-specialties section section--alt">
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
<section class="about-education section">
    <div class="container">
        <h2 class="section__title"><?= $page->education_title()->or('Formación') ?></h2>
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
<section class="about-experience section section--alt">
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
<section class="about-notable section">
    <div class="container">
        <h2 class="section__title"><?= $page->notable_places_title()->or('Lugares Destacados') ?></h2>
        <p class="about-notable__list"><?= $page->notable_places() ?></p>
    </div>
</section>
<?php endif ?>

<!-- Philosophy -->
<?php if ($page->philosophy()->isNotEmpty()): ?>
<section class="about-philosophy section section--alt">
    <div class="container container--narrow">
        <h2 class="section__title"><?= $page->philosophy_title()->or('Mi Filosofía') ?></h2>
        <div class="about-philosophy__content">
            <?= $page->philosophy()->kt() ?>
        </div>
    </div>
</section>
<?php endif ?>

<!-- Contact CTA -->
<?php if ($page->email()->isNotEmpty()): ?>
<section class="about-contact section">
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

<?php snippet('footer') ?>
