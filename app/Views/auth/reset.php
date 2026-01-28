<section class="card">
    <h1>Reset password</h1>
    <form method="post" action="<?= url('/reset-password') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="token" value="<?= h($token ?? '') ?>">
        <div class="form-group">
            <label>New password</label>
            <input type="password" name="password" required>
        </div>
        <button class="btn" type="submit">Update password</button>
    </form>
</section>
