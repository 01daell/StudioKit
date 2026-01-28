<section>
    <h1>Workspace Settings</h1>
    <div class="card">
        <h3>White-label PDF (Agency)</h3>
        <?php if (!empty($plan['white_label'])): ?>
            <p class="text-muted">Update white-label settings in config or future admin tools.</p>
        <?php else: ?>
            <p class="text-muted">White-label settings are available on the Agency plan.</p>
        <?php endif; ?>
        <p>Workspace: <?= h($workspace['name'] ?? '') ?></p>
    </div>
</section>
