<?php
$error = null;

if ($kirby->user()) {
    go(page('cuenta/perfil')->url());
}

if ($kirby->request()->is('POST')) {
    try {
        if (!csrf(get('csrf'))) {
            throw new Exception(t('account.error_csrf'));
        }

        $email = trim((string) get('email'));
        $password = (string) get('password');

        if ($email === '' || $password === '') {
            throw new Exception(t('account.error_required'));
        }

        $kirby->auth()->login($email, $password);

        $redirect = get('redirect');
        if ($redirect && str_starts_with($redirect, '/')) {
            go($redirect);
        }
        go(page('cuenta/perfil')->url());
    } catch (Exception $e) {
        $error = t('account.error_invalid_credentials');
    }
}
?>

<?php snippet('header') ?>

<section class="page-header">
    <div class="container">
        <h1 class="page-header__title"><?= $page->title() ?></h1>
    </div>
</section>

<section class="section">
    <div class="container container--narrow">
        <form method="post" class="auth-form">
            <?php if ($error): ?>
                <div class="auth-form__error"><?= esc($error) ?></div>
            <?php endif ?>

            <input type="hidden" name="csrf" value="<?= csrf() ?>">
            <?php if ($r = get('redirect')): ?>
                <input type="hidden" name="redirect" value="<?= esc($r) ?>">
            <?php endif ?>

            <label class="auth-form__label">
                <?= t('account.field_email') ?>
                <input type="email" name="email" value="<?= esc(get('email', '')) ?>" required>
            </label>

            <label class="auth-form__label">
                <?= t('account.field_password') ?>
                <input type="password" name="password" required>
            </label>

            <button type="submit" class="btn btn--primary btn--block">
                <?= t('account.sign_in') ?>
            </button>

            <p class="auth-form__alt">
                <?= t('account.no_account') ?>
                <a href="<?= page('cuenta/registrarse')->url() ?>"><?= t('account.create_account') ?></a>
            </p>
        </form>
    </div>
</section>

<?php snippet('footer') ?>
