<section>
    <div class="flex-between">
        <h1><?= h($kit['name']) ?></h1>
        <div>
            <a class="btn btn-outline" href="<?= url('/app/kits/' . $kit['id'] . '/export') ?>">Export</a>
            <a class="btn btn-outline" href="<?= url('/app/kits/' . $kit['id'] . '/share') ?>">Share</a>
        </div>
    </div>

    <div class="card">
        <h3>Kit Details</h3>
        <form method="post" action="<?= url('/app/kits/' . $kit['id'] . '/update') ?>">
            <?= csrf_field() ?>
            <div class="form-group">
                <label>Name</label>
                <input type="text" name="name" value="<?= h($kit['name']) ?>" required>
            </div>
            <div class="form-group">
                <label>Tagline</label>
                <input type="text" name="tagline" value="<?= h($kit['tagline']) ?>">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description"><?= h($kit['description']) ?></textarea>
            </div>
            <div class="form-group">
                <label>Voice Keywords</label>
                <input type="text" name="voice_keywords" value="<?= h(implode(',', json_decode($kit['voice_keywords'] ?? '[]', true) ?: [])) ?>">
            </div>
            <div class="form-group">
                <label>Usage Do</label>
                <textarea name="usage_do"><?= h($kit['usage_do']) ?></textarea>
            </div>
            <div class="form-group">
                <label>Usage Don't</label>
                <textarea name="usage_dont"><?= h($kit['usage_dont']) ?></textarea>
            </div>
            <button class="btn" type="submit">Save</button>
        </form>
    </div>

    <div class="card">
        <h3>Logos</h3>
        <form method="post" action="<?= url('/app/kits/' . $kit['id'] . '/assets/upload') ?>" enctype="multipart/form-data">
            <?= csrf_field() ?>
            <input type="file" name="asset" required>
            <button class="btn" type="submit">Upload</button>
        </form>
        <div class="grid grid-2" style="margin-top:12px;">
            <?php foreach ($assets as $asset): ?>
                <div class="card">
                    <div><strong><?= h($asset['original_name']) ?></strong></div>
                    <div class="text-muted">Type: <?= h($asset['type']) ?></div>
                    <form method="post" action="<?= url('/app/kits/' . $kit['id'] . '/assets/' . $asset['id'] . '/set-type') ?>">
                        <?= csrf_field() ?>
                        <select name="type">
                            <?php foreach (['primary_logo', 'icon', 'mono', 'other'] as $type): ?>
                                <option value="<?= $type ?>" <?= $type === $asset['type'] ? 'selected' : '' ?>><?= $type ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-outline" type="submit">Update</button>
                    </form>
                    <form method="post" action="<?= url('/app/kits/' . $kit['id'] . '/assets/' . $asset['id'] . '/delete') ?>">
                        <?= csrf_field() ?>
                        <button class="btn btn-outline" type="submit">Delete</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="card">
        <h3>Colors</h3>
        <form method="post" action="<?= url('/app/kits/' . $kit['id'] . '/colors/save') ?>">
            <?= csrf_field() ?>
            <?php for ($i = 0; $i < max(3, count($colors)); $i++): ?>
                <div class="form-group">
                    <label>Color <?= $i + 1 ?></label>
                    <input type="text" name="color_name[]" value="<?= h($colors[$i]['name'] ?? '') ?>" placeholder="Name">
                    <input type="text" name="color_hex[]" value="<?= h($colors[$i]['hex'] ?? '') ?>" placeholder="#FFFFFF">
                </div>
            <?php endfor; ?>
            <button class="btn" type="submit">Save Colors</button>
        </form>

        <div style="margin-top:16px;">
            <h4>Reorder Colors</h4>
            <form method="post" action="<?= url('/app/kits/' . $kit['id'] . '/colors/reorder') ?>" data-reorder-form>
                <?= csrf_field() ?>
                <input type="hidden" name="order" value="">
                <div data-sortable>
                    <?php foreach ($colors as $color): ?>
                        <div class="card sortable-item" data-id="<?= h($color['id']) ?>">
                            <div class="flex-between">
                                <div class="flex" style="gap:12px; align-items:center;">
                                    <div class="color-chip" style="background: <?= h($color['hex']) ?>"></div>
                                    <div><?= h($color['name']) ?> (<?= h($color['hex']) ?>)</div>
                                </div>
                                <button class="btn btn-outline" type="button" data-copy="<?= h($color['hex']) ?>">Copy</button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button class="btn" type="submit">Save Order</button>
            </form>
        </div>

        <div style="margin-top:16px;">
            <h4>Contrast Helper</h4>
            <p class="text-muted">Pick two colors to check WCAG contrast.</p>
            <div class="flex" style="gap:12px; align-items:center;">
                <input type="text" id="contrast-a" placeholder="#000000">
                <input type="text" id="contrast-b" placeholder="#ffffff">
                <button class="btn btn-outline" type="button" id="contrast-check">Check</button>
            </div>
            <div id="contrast-result" class="text-muted" style="margin-top:8px;"></div>
        </div>
    </div>

    <div class="card">
        <h3>Fonts</h3>
        <form method="post" action="<?= url('/app/kits/' . $kit['id'] . '/fonts/save') ?>">
            <?= csrf_field() ?>
            <div class="form-group">
                <label>Heading Font</label>
                <select name="heading_font">
                    <?php foreach (['Inter','Poppins','Montserrat','Roboto','Playfair Display','Source Sans Pro'] as $font): ?>
                        <option value="<?= h($font) ?>" <?= ($fonts['heading_font'] ?? '') === $font ? 'selected' : '' ?>><?= h($font) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Body Font</label>
                <select name="body_font">
                    <?php foreach (['Inter','Poppins','Montserrat','Roboto','Source Sans Pro','Lora'] as $font): ?>
                        <option value="<?= h($font) ?>" <?= ($fonts['body_font'] ?? '') === $font ? 'selected' : '' ?>><?= h($font) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button class="btn" type="submit">Save Fonts</button>
        </form>
    </div>

    <div class="card">
        <h3>Templates</h3>
        <form method="post" action="<?= url('/app/kits/' . $kit['id'] . '/templates/generate') ?>">
            <?= csrf_field() ?>
            <div class="form-group">
                <label>Primary Color</label>
                <input type="text" name="primary_color" value="#4f46e5">
            </div>
            <div class="form-group">
                <label>Email Signature Name</label>
                <input type="text" name="sig_name" value="<?= h($kit['name']) ?>">
            </div>
            <div class="form-group">
                <label>Email Signature Title</label>
                <input type="text" name="sig_title" value="Brand Manager">
            </div>
            <button class="btn" type="submit">Generate Templates</button>
        </form>
        <div class="grid grid-2" style="margin-top:12px;">
            <?php foreach ($templates as $template): ?>
                <div class="card">
                    <strong><?= h($template['type']) ?></strong>
                    <p class="text-muted">Saved asset: <?= h($template['path']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="card">
        <form method="post" action="<?= url('/app/kits/' . $kit['id'] . '/delete') ?>">
            <?= csrf_field() ?>
            <button class="btn btn-outline" type="submit">Delete Kit</button>
        </form>
    </div>
</section>

<script>
const contrastBtn = document.getElementById('contrast-check');
if (contrastBtn) {
  contrastBtn.addEventListener('click', () => {
    const a = document.getElementById('contrast-a').value;
    const b = document.getElementById('contrast-b').value;
    const ratio = window.StudioKitContrast(a, b);
    const result = document.getElementById('contrast-result');
    if (!ratio) {
      result.textContent = 'Invalid colors.';
      return;
    }
    result.textContent = `Contrast ratio: ${ratio.toFixed(2)} - ${ratio >= 4.5 ? 'AA Pass' : 'AA Fail'}`;
  });
}
</script>
