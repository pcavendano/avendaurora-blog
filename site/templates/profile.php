<?php
$user = $kirby->user();

if (!$user) {
    go(page('cuenta/iniciar-sesion')->url() . '?redirect=' . urlencode($page->url()));
}

$error = null;
$success = false;

if ($kirby->request()->is('POST')) {
    try {
        if (!csrf(get('csrf'))) {
            throw new Exception(t('account.error_csrf'));
        }

        $kirby->impersonate('kirby');
        $user = $user->update([
            'display_name' => trim((string) get('display_name')),
            'language'     => get('language'),
            'newsletter'   => get('newsletter') ? 'true' : 'false',
        ]);
        $kirby->impersonate(null);
        $success = true;
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

$favorites = $user->favorites()->toPages();
?>

<?php snippet('header') ?>

<section class="page-header">
    <div class="container">
        <h1 class="page-header__title"><?= esc($user->display_name()->or($user->email())) ?></h1>
        <p class="page-header__description"><?= esc($user->email()) ?></p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="profile-grid">

            <aside class="profile-sidebar">
                <h2><?= t('account.settings') ?></h2>

                <?php if ($success): ?>
                    <div class="auth-form__success"><?= t('account.saved') ?></div>
                <?php endif ?>
                <?php if ($error): ?>
                    <div class="auth-form__error"><?= esc($error) ?></div>
                <?php endif ?>

                <form method="post" class="auth-form">
                    <input type="hidden" name="csrf" value="<?= csrf() ?>">

                    <label class="auth-form__label">
                        <?= t('account.field_name') ?>
                        <input type="text" name="display_name" value="<?= esc($user->display_name()) ?>">
                    </label>

                    <label class="auth-form__label">
                        <?= t('account.field_language') ?>
                        <select name="language">
                            <option value="es" <?= $user->language()->value() === 'es' ? 'selected' : '' ?>>Español</option>
                            <option value="en" <?= $user->language()->value() === 'en' ? 'selected' : '' ?>>English</option>
                            <option value="fr" <?= $user->language()->value() === 'fr' ? 'selected' : '' ?>>Français</option>
                        </select>
                    </label>

                    <label class="auth-form__checkbox">
                        <input type="checkbox" name="newsletter" value="1" <?= $user->newsletter()->toBool() ? 'checked' : '' ?>>
                        <?= t('account.newsletter') ?>
                    </label>

                    <button type="submit" class="btn btn--primary btn--block"><?= t('account.save') ?></button>
                </form>

                <a href="<?= url('cuenta/salir') ?>" class="profile-logout"><?= t('account.sign_out') ?></a>
            </aside>

            <div class="profile-main">
                <h2><?= t('account.favorites') ?> (<?= $favorites->count() ?>)</h2>

                <?php if ($favorites->count() === 0): ?>
                    <div class="empty-state">
                        <p><?= t('account.no_favorites') ?></p>
                        <a href="<?= page('recetas')->url() ?>" class="btn btn--secondary">
                            <?= t('account.browse_recipes') ?>
                        </a>
                    </div>
                <?php else: ?>
                    <div class="recipe-grid">
                        <?php foreach ($favorites as $recipe): ?>
                            <?php snippet('recipe-card', ['recipe' => $recipe]) ?>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>
            </div>

        </div>
    </div>
</section>

<?php snippet('footer') ?>
