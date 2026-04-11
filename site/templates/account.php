<?php snippet('header') ?>

<?php
if ($kirby->user()) {
    go(page('cuenta/perfil')->url());
}
?>

<section class="page-header">
    <div class="container">
        <h1 class="page-header__title"><?= $page->title() ?></h1>
    </div>
</section>

<section class="section">
    <div class="container container--narrow">
        <div class="auth-choice">
            <div class="auth-choice__card">
                <h2><?= t('account.have_account') ?></h2>
                <p><?= t('account.have_account_desc') ?></p>
                <a href="<?= page('cuenta/iniciar-sesion')->url() ?>" class="btn btn--primary">
                    <?= t('account.sign_in') ?>
                </a>
            </div>
            <div class="auth-choice__card">
                <h2><?= t('account.no_account') ?></h2>
                <p><?= t('account.no_account_desc') ?></p>
                <a href="<?= page('cuenta/registrarse')->url() ?>" class="btn btn--secondary">
                    <?= t('account.create_account') ?>
                </a>
            </div>
        </div>
    </div>
</section>

<?php snippet('footer') ?>
