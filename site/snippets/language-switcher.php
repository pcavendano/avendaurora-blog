<div class="language-switcher">
    <button class="language-switcher__current">
        <?= strtoupper($kirby->language()->code()) ?>
        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="m6 9 6 6 6-6"/>
        </svg>
    </button>
    <ul class="language-switcher__dropdown">
        <?php foreach ($kirby->languages() as $language): ?>
        <li>
            <a href="<?= $page->url($language->code()) ?>"
               class="<?= $language->code() === $kirby->language()->code() ? 'is-active' : '' ?>"
               hreflang="<?= $language->code() ?>">
                <?= $language->name() ?>
            </a>
        </li>
        <?php endforeach ?>
    </ul>
</div>
