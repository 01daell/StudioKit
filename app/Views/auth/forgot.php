<section class="card">
    <h1>Forgot password</h1>
    <form method="post" action="<?= url('/forgot-password') ?>">
        <?= csrf_field() ?>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <button class="btn" type="submit">Send reset link</button>
    </form>
</section>
