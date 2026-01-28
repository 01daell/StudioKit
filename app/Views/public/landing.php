<section class="hero">
    <h1>Pricing that scales with your brand</h1>
    <p class="text-muted">Start free. Upgrade when you’re ready to export, share, and manage multiple client kits.</p>
    <p>StudioKit helps you build a clean, consistent brand kit—logos, colors, typography, and templates—in minutes.</p>
    <div class="flex" style="gap:12px; margin-top:20px;">
        <a class="btn" href="<?= url('/sign-up') ?>">Get Started Free</a>
        <a class="btn btn-outline" href="<?= url('/pricing') ?>">View Plans</a>
    </div>
</section>

<section class="grid grid-3">
    <?php foreach ($plans as $key => $plan): ?>
        <div class="card">
            <div class="flex-between">
                <h3><?= h($plan['name']) ?></h3>
                <?php if ($key === 'pro'): ?>
                    <span class="badge">Most Popular</span>
                <?php elseif ($key === 'agency'): ?>
                    <span class="badge">For Agencies</span>
                <?php endif; ?>
            </div>
            <p class="text-muted"><?= h($plan['name']) ?> includes <?= $plan['kits'] === PHP_INT_MAX ? 'unlimited kits' : $plan['kits'] . ' kits' ?>.</p>
            <a class="btn" href="<?= url('/pricing') ?>">Compare Plans</a>
        </div>
    <?php endforeach; ?>
</section>
