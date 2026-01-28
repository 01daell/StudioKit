<section class="card">
    <h1>Share <?= h($kit['name']) ?></h1>
    <?php if (empty($plan['share_links'])): ?>
        <p class="text-muted">Share links are available on the Pro plan.</p>
    <?php else: ?>
        <?php if (!empty($link)): ?>
            <p>Share URL:</p>
            <div class="card">
                <code><?= h(url('/share/' . $link['token'])) ?></code>
            </div>
            <form method="post" action="<?= url('/app/kits/' . $kit['id'] . '/share/revoke') ?>">
                <?= csrf_field() ?>
                <button class="btn btn-outline" type="submit">Revoke Link</button>
            </form>
        <?php else: ?>
            <form method="post" action="<?= url('/app/kits/' . $kit['id'] . '/share/create') ?>">
                <?= csrf_field() ?>
                <button class="btn" type="submit">Create Share Link</button>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</section>
