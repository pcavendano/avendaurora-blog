<?php
/** @var \Kirby\Cms\Block $block */
$entries = $block->entries()->toStructure();
if ($entries->count() === 0) {
    return;
}
?>
<section class="about-experience section">
    <div class="container">
        <?php if ($block->title()->isNotEmpty()): ?>
            <h2 class="section__title"><?= $block->title() ?></h2>
        <?php endif ?>
        <div class="about-timeline">
            <?php foreach ($entries as $entry): ?>
                <div class="about-timeline__item">
                    <?php if ($entry->period()->isNotEmpty()): ?>
                        <div class="about-timeline__year"><?= $entry->period() ?></div>
                    <?php endif ?>
                    <div class="about-timeline__content">
                        <?php if ($entry->position()->isNotEmpty()): ?>
                            <h3 class="about-timeline__title"><?= $entry->position() ?></h3>
                        <?php endif ?>
                        <?php if ($entry->place()->isNotEmpty()): ?>
                            <p class="about-timeline__institution"><?= $entry->place() ?></p>
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
