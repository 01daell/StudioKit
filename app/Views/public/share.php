<section>
    <h1><?= h($kit['name'] ?? 'Brand Kit') ?></h1>
    <p class="text-muted"><?= h($kit['tagline'] ?? '') ?></p>
    <div class="card">
        <h3>Description</h3>
        <p><?= h($kit['description'] ?? '') ?></p>
    </div>
    <div class="card">
        <h3>Colors</h3>
        <div class="grid grid-2">
            <?php foreach ($colors as $color): ?>
                <div class="flex-between">
                    <div class="flex" style="gap:12px; align-items:center;">
                        <div class="color-chip" style="background: <?= h($color['hex']) ?>"></div>
                        <div>
                            <strong><?= h($color['name']) ?></strong>
                            <div class="text-muted"><?= h($color['hex']) ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="card">
        <h3>Typography</h3>
        <p class="text-muted">Heading: <?= h($fonts['heading_font'] ?? 'Not set') ?></p>
        <p class="text-muted">Body: <?= h($fonts['body_font'] ?? 'Not set') ?></p>
    </div>
    <div class="card">
        <h3>Logos</h3>
        <div class="grid grid-2">
            <?php foreach ($assets as $asset): ?>
                <div class="card">
                    <div><strong><?= h($asset['original_name']) ?></strong></div>
                    <div class="text-muted">Type: <?= h($asset['type']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php if (!empty($plan['zip_export'])): ?>
        <a class="btn" href="<?= url('/share/' . $token . '?download=zip') ?>">Download ZIP Package</a>
    <?php endif; ?>
</section>
