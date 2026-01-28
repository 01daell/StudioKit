<section class="card">
    <div class="progress">
        <div class="step">Welcome</div>
        <div class="step">Requirements</div>
        <div class="step">Database</div>
        <div class="step">App</div>
        <div class="step">Email</div>
        <div class="step">Stripe</div>
        <div class="step active">Install</div>
    </div>
    <h1>Ready to Install</h1>
    <p class="text-muted">Click install to write config, create tables, and set up your first admin user.</p>
    <form method="post" action="<?= url('/install') ?>">
        <button class="btn" type="submit">Install StudioKit</button>
    </form>
</section>
