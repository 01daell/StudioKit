<section class="card">
    <div class="progress">
        <div class="step">Welcome</div>
        <div class="step active">Requirements</div>
        <div class="step">Database</div>
        <div class="step">App</div>
        <div class="step">Email</div>
        <div class="step">Stripe</div>
        <div class="step">Install</div>
    </div>
    <h1>Requirements Check</h1>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error"><?php foreach ($errors as $error) { echo h($error) . '<br>'; } ?></div>
    <?php endif; ?>
    <ul class="list">
        <?php foreach ($requirements as $req): ?>
            <li><?= h($req['label']) ?>: <?= $req['status'] ? '✅' : '❌' ?></li>
        <?php endforeach; ?>
    </ul>
    <form method="post" action="<?= url('/install') ?>">
        <button class="btn" type="submit">Continue</button>
    </form>
</section>
