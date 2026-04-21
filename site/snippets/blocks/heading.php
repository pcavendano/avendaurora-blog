<?php
/** @var \Kirby\Cms\Block $block */
$level = $block->level()->or('h2')->value();
$allowedLevels = ['h1', 'h2', 'h3'];
if (!in_array($level, $allowedLevels, true)) {
    $level = 'h2';
}
?>
<section class="about-heading section">
    <div class="container container--narrow">
        <<?= $level ?> class="section__title"><?= $block->text() ?></<?= $level ?>>
    </div>
</section>
