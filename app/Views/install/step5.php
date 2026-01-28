<section class="card">
    <div class="progress">
        <div class="step">Welcome</div>
        <div class="step">Requirements</div>
        <div class="step">Database</div>
        <div class="step">App</div>
        <div class="step active">Email</div>
        <div class="step">Stripe</div>
        <div class="step">Install</div>
    </div>
    <h1>Email (SMTP)</h1>
    <p class="text-muted">Configure your SMTP settings from cPanel.</p>
    <form method="post" action="<?= url('/install') ?>">
        <div class="form-group">
            <label>SMTP Host</label>
            <input type="text" name="smtp_host" value="<?= h($data['smtp']['host'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>SMTP Port</label>
            <input type="text" name="smtp_port" value="<?= h($data['smtp']['port'] ?? '587') ?>">
        </div>
        <div class="form-group">
            <label>SMTP Username</label>
            <input type="text" name="smtp_user" value="<?= h($data['smtp']['user'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>SMTP Password</label>
            <input type="password" name="smtp_pass" value="<?= h($data['smtp']['pass'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>Encryption</label>
            <select name="smtp_encryption">
                <option value="tls" <?= ($data['smtp']['encryption'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS</option>
                <option value="ssl" <?= ($data['smtp']['encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                <option value="none" <?= ($data['smtp']['encryption'] ?? '') === 'none' ? 'selected' : '' ?>>None</option>
            </select>
        </div>
        <div class="form-group">
            <label>From Name</label>
            <input type="text" name="smtp_from_name" value="<?= h($data['smtp']['from_name'] ?? 'StudioKit') ?>">
        </div>
        <div class="form-group">
            <label>From Email</label>
            <input type="email" name="smtp_from_email" value="<?= h($data['smtp']['from_email'] ?? '') ?>">
        </div>
        <button class="btn" type="submit">Continue</button>
    </form>
</section>
