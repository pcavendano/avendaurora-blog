<?php
/** @var \Kirby\Cms\Block $block */
$entries = $block->entries()->toStructure();
if ($entries->count() === 0) {
    return;
}
?>
<section class="about-dishes section section--alt">
    <div class="container">
        <?php if ($block->title()->isNotEmpty()): ?>
            <h2 class="section__title text-center"><?= $block->title() ?></h2>
        <?php endif ?>
        <div class="about-dishes__grid">
            <?php foreach ($entries as $entry): ?>
                <div class="about-dishes__item">
                    <?php if ($image = $entry->image()->toFile()): ?>
                        <div class="about-dishes__image">
                            <?php snippet('responsive-image', [
                                'image' => $image,
                                'widths' => [400, 600, 900],
                                'sizes' => '(max-width: 768px) 100vw, 50vw',
                                'alt' => $image->alt()->or($entry->title())->value(),
                            ]) ?>
                        </div>
                    <?php endif ?>
                    <div class="about-dishes__content">
                        <?php if ($entry->title()->isNotEmpty()): ?>
                            <h3 class="about-dishes__title"><?= $entry->title() ?></h3>
                        <?php endif ?>
                        <?php if ($entry->description()->isNotEmpty()): ?>
                            <p class="about-dishes__description"><?= $entry->description() ?></p>
                        <?php endif ?>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</section>
