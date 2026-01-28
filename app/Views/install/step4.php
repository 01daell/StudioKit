<section class="card">
    <div class="progress">
        <div class="step">Welcome</div>
        <div class="step">Requirements</div>
        <div class="step">Database</div>
        <div class="step active">App</div>
        <div class="step">Email</div>
        <div class="step">Stripe</div>
        <div class="step">Install</div>
    </div>
    <h1>App Settings</h1>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error"><?php foreach ($errors as $error) { echo h($error) . '<br>'; } ?></div>
    <?php endif; ?>
    <form method="post" action="<?= url('/install') ?>">
        <div class="form-group">
            <label>App URL</label>
            <input type="text" name="app_url" value="<?= h($data['app']['url'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label>App Name</label>
            <input type="text" name="app_name" value="<?= h($data['app']['name'] ?? 'StudioKit') ?>">
        </div>
        <div class="form-group">
            <label>Admin Name</label>
            <input type="text" name="admin_name" value="<?= h($data['admin']['name'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label>Admin Email</label>
            <input type="email" name="admin_email" value="<?= h($data['admin']['email'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label>Admin Password</label>
            <input type="password" name="admin_password" required>
        </div>
        <div class="form-group">
            <label>Default Workspace Name</label>
            <input type="text" name="workspace_name" value="<?= h($data['workspace']['name'] ?? 'StudioKit Workspace') ?>" required>
        </div>
        <button class="btn" type="submit">Continue</button>
    </form>
</section>
