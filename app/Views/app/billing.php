<section>
    <h1>Billing</h1>
    <div class="card">
        <h3>Current Plan: <?= h($plan['name']) ?></h3>
        <p class="text-muted">Status: <?= h($subscription['status'] ?? 'inactive') ?></p>
        <?php if (!empty($subscription['stripe_customer_id'])): ?>
            <a class="btn btn-outline" href="<?= url('/app/billing/portal') ?>">Open Customer Portal</a>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3>Upgrade</h3>
        <form method="post" action="<?= url('/app/billing/checkout') ?>">
            <?= csrf_field() ?>
            <select name="plan">
                <option value="starter">Starter</option>
                <option value="pro">Pro</option>
                <option value="agency">Agency</option>
            </select>
            <button class="btn" type="submit">Launch Stripe Checkout</button>
        </form>
        <?php if (empty($priceIds['starter']) || empty($priceIds['pro']) || empty($priceIds['agency'])): ?>
            <p class="notice">Stripe price IDs are missing. Add them in config/config.php.</p>
        <?php endif; ?>
    </div>
</section>
