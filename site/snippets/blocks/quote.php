<?php
/** @var \Kirby\Cms\Block $block */
if ($block->text()->isEmpty()) {
    return;
}
?>
<section class="about-bio section">
    <div class="container container--narrow">
        <blockquote class="about-bio__quote">
            <p><?= $block->text() ?></p>
            <?php if ($block->citation()->isNotEmpty()): ?>
                <cite style="display:block;margin-top:1rem;font-style:normal;opacity:.7;"><?= $block->citation() ?></cite>
            <?php endif ?>
        </blockquote>
    </div>
</section>
