<div class="language-switcher" id="languageSwitcher">
    <button type="button" class="language-switcher__current" id="languageSwitcherToggle" aria-haspopup="true" aria-expanded="false" aria-label="<?= t('nav.search') ?>">
        <span class="language-switcher__code"><?= strtoupper($kirby->language()->code()) ?></span>
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="language-switcher__chevron">
            <path d="m6 9 6 6 6-6"/>
        </svg>
    </button>
    <ul class="language-switcher__dropdown" role="menu">
        <?php foreach ($kirby->languages() as $language): ?>
        <li role="none">
            <a href="<?= $page->url($language->code()) ?>"
               class="<?= $language->code() === $kirby->language()->code() ? 'is-active' : '' ?>"
               hreflang="<?= $language->code() ?>"
               role="menuitem">
                <?= $language->name() ?>
            </a>
        </li>
        <?php endforeach ?>
    </ul>
</div>
