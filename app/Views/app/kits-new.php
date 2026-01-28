<section class="card">
    <h1>New Brand Kit</h1>
    <form method="post" action="<?= url('/app/kits') ?>">
        <?= csrf_field() ?>
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" required>
        </div>
        <div class="form-group">
            <label>Tagline</label>
            <input type="text" name="tagline">
        </div>
        <div class="form-group">
            <label>Description</label>
            <textarea name="description"></textarea>
        </div>
        <div class="form-group">
            <label>Voice Keywords (comma separated)</label>
            <input type="text" name="voice_keywords">
        </div>
        <div class="form-group">
            <label>Usage Do</label>
            <textarea name="usage_do"></textarea>
        </div>
        <div class="form-group">
            <label>Usage Don't</label>
            <textarea name="usage_dont"></textarea>
        </div>
        <button class="btn" type="submit">Create Kit</button>
    </form>
</section>
