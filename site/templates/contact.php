<?php PageStats::record($page->id()); ?>
<?php snippet('header') ?>

<article class="contact-page">

    <section class="contact-page__hero">
        <div class="container container--narrow">
            <h1 class="contact-page__title"><?= $page->title() ?></h1>
            <?php if ($page->intro()->isNotEmpty()): ?>
                <p class="contact-page__intro"><?= $page->intro() ?></p>
            <?php endif ?>
        </div>
    </section>

    <section class="contact-page__body" id="contacto">
        <div class="container container--narrow">

            <div class="contact-card">
                <?php if ($page->email()->isNotEmpty()): ?>
                    <a href="mailto:<?= $page->email() ?>" class="contact-card__primary">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                            <polyline points="22,6 12,13 2,6"/>
                        </svg>
                        <?= $page->email() ?>
                    </a>
                <?php endif ?>

                <dl class="contact-card__details">
                    <?php if ($page->phone()->isNotEmpty()): ?>
                        <div class="contact-card__row">
                            <dt><?= t('contact.phone') ?></dt>
                            <dd><a href="tel:<?= $page->phone() ?>"><?= $page->phone() ?></a></dd>
                        </div>
                    <?php endif ?>
                    <?php if ($page->location()->isNotEmpty()): ?>
                        <div class="contact-card__row">
                            <dt><?= t('contact.location') ?></dt>
                            <dd><?= $page->location() ?></dd>
                        </div>
                    <?php endif ?>
                </dl>
            </div>

            <?php if ($page->body()->isNotEmpty()): ?>
                <div class="contact-page__note">
                    <?= $page->body()->kt() ?>
                </div>
            <?php endif ?>

            <?php if ($site->instagram()->isNotEmpty() || $site->facebook()->isNotEmpty() || $site->youtube()->isNotEmpty()): ?>
                <div class="contact-page__social">
                    <span class="contact-page__social-label"><?= t('contact.follow') ?></span>
                    <?php if ($site->instagram()->isNotEmpty()): ?>
                        <a href="<?= $site->instagram() ?>" target="_blank" rel="noopener">Instagram</a>
                    <?php endif ?>
                    <?php if ($site->facebook()->isNotEmpty()): ?>
                        <a href="<?= $site->facebook() ?>" target="_blank" rel="noopener">Facebook</a>
                    <?php endif ?>
                    <?php if ($site->youtube()->isNotEmpty()): ?>
                        <a href="<?= $site->youtube() ?>" target="_blank" rel="noopener">YouTube</a>
                    <?php endif ?>
                </div>
            <?php endif ?>

        </div>
    </section>

</article>

<?php snippet('footer') ?>
