<?php
/** @var \Kirby\Cms\Block $block */
if ($block->text()->isEmpty()) {
    return;
}
?>
<section class="about-bio section">
    <div class="container container--narrow">
        <div class="about-bio__content">
            <?= $block->text() ?>
        </div>
    </div>
</section>
