<?php
/**
 * Responsive image with automatic srcset/sizes and alt from file metadata.
 *
 * @var \Kirby\Cms\File|null $image
 * @var array  $widths   Pixel widths to generate (default 400-1600)
 * @var string $sizes    CSS sizes attribute (default "100vw")
 * @var string $alt      Optional alt override; falls back to file's alt, then caption
 * @var string $class    Optional CSS class
 * @var bool   $eager    Load eagerly (above the fold); default lazy
 */
if (empty($image) || !$image->isResizable()) {
    return;
}

$widths = $widths ?? [400, 800, 1200, 1600];
$sizes  = $sizes  ?? '100vw';
$class  = $class  ?? '';
$eager  = $eager  ?? false;
$alt    = $alt    ?? $image->alt()->or($image->caption()->excerpt(80))->value() ?? '';
$maxWidth = max($widths);
?>
<img src="<?= $image->thumb(['width' => $maxWidth])->url() ?>"
     srcset="<?= esc($image->srcset($widths)) ?>"
     sizes="<?= esc($sizes) ?>"
     alt="<?= esc($alt) ?>"<?php if ($class): ?>
     class="<?= esc($class) ?>"<?php endif ?>
     loading="<?= $eager ? 'eager' : 'lazy' ?>"
     decoding="async">