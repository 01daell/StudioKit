<section class="card">
    <div class="progress">
        <div class="step">Welcome</div>
        <div class="step">Requirements</div>
        <div class="step">Database</div>
        <div class="step">App</div>
        <div class="step">Email</div>
        <div class="step active">Stripe</div>
        <div class="step">Install</div>
    </div>
    <h1>Stripe Settings</h1>
    <p class="notice">Price IDs are optional. If left blank, manual setup is required later.</p>
    <form method="post" action="<?= url('/install') ?>">
        <div class="form-group">
            <label>Publishable Key</label>
            <input type="text" name="stripe_publishable_key" value="<?= h($data['stripe']['publishable_key'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Secret Key</label>
            <input type="text" name="stripe_secret_key" value="<?= h($data['stripe']['secret_key'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Webhook Signing Secret</label>
            <input type="text" name="stripe_webhook_secret" value="<?= h($data['stripe']['webhook_secret'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Success URL</label>
            <input type="text" name="stripe_success_url" value="<?= h($data['stripe']['success_url'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Cancel URL</label>
            <input type="text" name="stripe_cancel_url" value="<?= h($data['stripe']['cancel_url'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Starter Price ID</label>
            <input type="text" name="stripe_price_starter" value="<?= h($data['stripe']['price_ids']['starter'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Pro Price ID</label>
            <input type="text" name="stripe_price_pro" value="<?= h($data['stripe']['price_ids']['pro'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Agency Price ID</label>
            <input type="text" name="stripe_price_agency" value="<?= h($data['stripe']['price_ids']['agency'] ?? '') ?>">
        </div>
        <button class="btn" type="submit">Continue</button>
    </form>
</section>
