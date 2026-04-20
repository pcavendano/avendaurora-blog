<?php
/** @var \Kirby\Cms\Block $block */
$images  = $block->images()->toFiles();
$layout  = $block->layout()->or('grid')->value();
$columns = $block->columns()->or(3)->toInt();
$showCaptions = $block->show_captions()->toBool();

if ($images->count() === 0) {
    return;
}
?>
<figure class="gallery gallery--<?= esc($layout) ?>" style="--gallery-columns: <?= $columns ?>;">
    <?php foreach ($images as $image): ?>
        <div class="gallery__item">
            <a href="<?= $image->url() ?>" class="gallery__link" data-lightbox="gallery-<?= $block->id() ?>">
                <?php snippet('responsive-image', [
                    'image' => $image,
                    'widths' => [400, 800, 1200],
                    'sizes' => '(max-width: 600px) 100vw, ' . (100 / $columns) . 'vw'
                ]) ?>
            </a>
            <?php if ($showCaptions && $image->caption()->isNotEmpty()): ?>
                <figcaption class="gallery__caption"><?= $image->caption() ?></figcaption>
            <?php endif ?>
        </div>
    <?php endforeach ?>
</figure>