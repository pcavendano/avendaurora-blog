<?php
/** @var \Kirby\Cms\Block $block */
$entries = $block->entries()->toStructure();
if ($entries->count() === 0) {
    return;
}
?>
<section class="about-education section section--alt">
    <div class="container">
        <?php if ($block->title()->isNotEmpty()): ?>
            <h2 class="section__title"><?= $block->title() ?></h2>
        <?php endif ?>
        <div class="about-timeline">
            <?php foreach ($entries as $entry): ?>
                <div class="about-timeline__item">
                    <?php if ($entry->year()->isNotEmpty()): ?>
                        <div class="about-timeline__year"><?= $entry->year() ?></div>
                    <?php endif ?>
                    <div class="about-timeline__content">
                        <?php if ($entry->title()->isNotEmpty()): ?>
                            <h3 class="about-timeline__title"><?= $entry->title() ?></h3>
                        <?php endif ?>
                        <?php if ($entry->institution()->isNotEmpty()): ?>
                            <p class="about-timeline__institution"><?= $entry->institution() ?></p>
                        <?php endif ?>
                        <?php if ($entry->description()->isNotEmpty()): ?>
                            <p class="about-timeline__description"><?= $entry->description() ?></p>
                        <?php endif ?>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</section>
