<?php
$error = null;
$success = false;

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
        $passwordConfirm = (string) get('password_confirm');
        $name = trim((string) get('name'));

        if ($email === '' || $password === '') {
            throw new Exception(t('account.error_required'));
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception(t('account.error_invalid_email'));
        }
        if (strlen($password) < 8) {
            throw new Exception(t('account.error_password_short'));
        }
        if ($password !== $passwordConfirm) {
            throw new Exception(t('account.error_password_mismatch'));
        }
        if ($kirby->users()->find($email)) {
            throw new Exception(t('account.error_email_taken'));
        }

        $kirby->impersonate('kirby');
        $user = $kirby->users()->create([
            'email'    => $email,
            'password' => $password,
            'role'     => 'member',
            'language' => $kirby->language()->code(),
            'content'  => [
                'display_name' => $name,
                'language'     => $kirby->language()->code(),
                'newsletter'   => 'true',
            ]
        ]);
        $kirby->impersonate(null);

        $user->loginPasswordless();

        go(page('cuenta/perfil')->url());
    } catch (Exception $e) {
        $error = $e->getMessage();
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

            <label class="auth-form__label">
                <?= t('account.field_name') ?>
                <input type="text" name="name" value="<?= esc(get('name', '')) ?>" required>
            </label>

            <label class="auth-form__label">
                <?= t('account.field_email') ?>
                <input type="email" name="email" value="<?= esc(get('email', '')) ?>" required>
            </label>

            <label class="auth-form__label">
                <?= t('account.field_password') ?>
                <input type="password" name="password" required minlength="8">
                <small><?= t('account.password_help') ?></small>
            </label>

            <label class="auth-form__label">
                <?= t('account.field_password_confirm') ?>
                <input type="password" name="password_confirm" required minlength="8">
            </label>

            <button type="submit" class="btn btn--primary btn--block">
                <?= t('account.create_account') ?>
            </button>

            <p class="auth-form__alt">
                <?= t('account.have_account') ?>
                <a href="<?= page('cuenta/iniciar-sesion')->url() ?>"><?= t('account.sign_in') ?></a>
            </p>
        </form>
    </div>
</section>

<?php snippet('footer') ?>
