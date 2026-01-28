<section class="card">
    <div class="progress">
        <div class="step">Welcome</div>
        <div class="step">Requirements</div>
        <div class="step active">Database</div>
        <div class="step">App</div>
        <div class="step">Email</div>
        <div class="step">Stripe</div>
        <div class="step">Install</div>
    </div>
    <h1>Database Setup</h1>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error"><?php foreach ($errors as $error) { echo h($error) . '<br>'; } ?></div>
    <?php endif; ?>
    <form method="post" action="<?= url('/install') ?>">
        <div class="form-group">
            <label>DB Host</label>
            <input type="text" name="db_host" value="<?= h($data['database']['host'] ?? 'localhost') ?>" required>
        </div>
        <div class="form-group">
            <label>DB Name</label>
            <input type="text" name="db_name" value="<?= h($data['database']['name'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label>DB User</label>
            <input type="text" name="db_user" value="<?= h($data['database']['user'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label>DB Password</label>
            <input type="password" name="db_pass" value="<?= h($data['database']['pass'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>DB Port</label>
            <input type="text" name="db_port" value="<?= h($data['database']['port'] ?? '3306') ?>">
        </div>
        <button class="btn" type="submit">Test & Continue</button>
    </form>
</section>
