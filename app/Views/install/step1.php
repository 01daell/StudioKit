<section class="card">
    <div class="progress">
        <div class="step active">Welcome</div>
        <div class="step">Requirements</div>
        <div class="step">Database</div>
        <div class="step">App</div>
        <div class="step">Email</div>
        <div class="step">Stripe</div>
        <div class="step">Install</div>
    </div>
    <h1>Welcome to StudioKit</h1>
    <p class="text-muted">This wizard will guide you through installation.</p>
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error"><?php foreach ($errors as $error) { echo h($error) . '<br>'; } ?></div>
    <?php endif; ?>
    <form method="post" action="<?= url('/install') ?>">
        <label><input type="checkbox" name="accept_terms"> I accept the license and terms.</label>
        <div style="margin-top:16px;">
            <button class="btn" type="submit">Start Install</button>
        </div>
    </form>
</section>
