<?php
use App\Core\Auth;
use App\Core\Session;
use App\Core\Config;

$user = Auth::user();
$appName = Config::get('app.name', 'StudioKit');
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($appName) ?></title>
    <link rel="stylesheet" href="<?= url('/assets/styles.css') ?>">
    <script src="<?= url('/assets/app.js') ?>" defer></script>
</head>
<body>
<div class="nav">
    <strong><?= h($appName) ?></strong>
    <div>
        <a href="<?= url('/') ?>">Home</a>
        <a href="<?= url('/pricing') ?>">Pricing</a>
        <a href="<?= url('/faq') ?>">FAQ</a>
        <?php if ($user): ?>
            <a href="<?= url('/app') ?>">Dashboard</a>
            <form action="<?= url('/sign-out') ?>" method="post" style="display:inline;">
                <?= csrf_field() ?>
                <button class="btn btn-outline" type="submit">Sign out</button>
            </form>
        <?php else: ?>
            <a href="<?= url('/sign-in') ?>">Sign in</a>
        <?php endif; ?>
    </div>
</div>

<div class="container">
    <?php if ($flash = Session::flash('message')): ?>
        <div class="alert alert-success"><?= h($flash) ?></div>
    <?php endif; ?>
    <?php if ($flash = Session::flash('error')): ?>
        <div class="alert alert-error"><?= h($flash) ?></div>
    <?php endif; ?>
    <?php require $contentView; ?>
</div>

<footer>
    <small>&copy; <?= date('Y') ?> <?= h($appName) ?>. All rights reserved.</small>
</footer>
</body>
</html>
