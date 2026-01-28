<section>
    <div class="flex-between">
        <h1>Dashboard</h1>
        <a class="btn" href="<?= url('/app/kits/new') ?>">New Brand Kit</a>
    </div>
    <div class="card">
        <h3>Current Plan: <?= h($plan['name']) ?></h3>
        <p class="text-muted">Kits limit: <?= $plan['kits'] === PHP_INT_MAX ? 'Unlimited' : $plan['kits'] ?></p>
        <a class="btn btn-outline" href="<?= url('/app/billing') ?>">Manage Billing</a>
    </div>

    <div class="card">
        <h3>Your Brand Kits</h3>
        <?php if (!$kits): ?>
            <p class="text-muted">No kits yet. Create your first kit.</p>
        <?php else: ?>
            <ul class="list">
                <?php foreach ($kits as $kit): ?>
                    <li><a href="<?= url('/app/kits/' . $kit['id']) ?>"><?= h($kit['name']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</section>
