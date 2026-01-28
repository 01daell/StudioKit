<section class="card">
    <h1>Sign in</h1>
    <form method="post" action="<?= url('/sign-in') ?>">
        <?= csrf_field() ?>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <button class="btn" type="submit">Sign in</button>
    </form>
    <p class="text-muted"><a href="<?= url('/forgot-password') ?>">Forgot password?</a></p>
</section>
