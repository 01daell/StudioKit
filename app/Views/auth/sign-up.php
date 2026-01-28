<section class="card">
    <h1>Create your account</h1>
    <form method="post" action="<?= url('/sign-up') ?>">
        <?= csrf_field() ?>
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <button class="btn" type="submit">Get Started Free</button>
    </form>
    <p class="text-muted">Already have an account? <a href="<?= url('/sign-in') ?>">Sign in</a></p>
</section>
