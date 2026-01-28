<section class="card">
    <h1>Export <?= h($kit['name']) ?></h1>
    <p class="text-muted">Export your kit as PDF or ZIP.</p>
    <div class="flex" style="gap:12px;">
        <a class="btn" href="<?= url('/app/kits/' . $kit['id'] . '/export/pdf') ?>">Download PDF</a>
        <?php if (!empty($plan['zip_export'])): ?>
            <a class="btn btn-outline" href="<?= url('/app/kits/' . $kit['id'] . '/export/zip') ?>">Download ZIP</a>
        <?php else: ?>
            <span class="text-muted">ZIP export available on Pro+</span>
        <?php endif; ?>
    </div>
</section>
