<section>
    <h1>Workspace Members</h1>
    <div class="card">
        <h3>Members</h3>
        <ul class="list">
            <?php foreach ($memberships as $member): ?>
                <li><?= h($member['name']) ?> - <?= h($member['role']) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="card">
        <h3>Invites</h3>
        <?php if (!empty($plan['invites'])): ?>
            <form method="post" action="<?= url('/app/workspace/invite') ?>">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Role</label>
                    <select name="role">
                        <option value="MEMBER">Member</option>
                        <option value="ADMIN">Admin</option>
                    </select>
                </div>
                <button class="btn" type="submit">Send Invite</button>
            </form>
        <?php else: ?>
            <p class="text-muted">Invites available on Agency plan.</p>
        <?php endif; ?>
        <ul class="list">
            <?php foreach ($invites as $invite): ?>
                <li><?= h($invite['email']) ?> - <?= h($invite['status']) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
