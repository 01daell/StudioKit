<?php
namespace App\Models;

use App\Core\DB;

class Subscription
{
    public static function findByWorkspace(int $workspaceId): ?array
    {
        $stmt = DB::pdo()->prepare('SELECT * FROM subscriptions WHERE workspace_id = ?');
        $stmt->execute([$workspaceId]);
        $sub = $stmt->fetch();
        return $sub ?: null;
    }

    public static function upsert(array $data): void
    {
        $stmt = DB::pdo()->prepare('INSERT INTO subscriptions (workspace_id, stripe_customer_id, stripe_subscription_id, status, plan, current_period_end, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
            ON DUPLICATE KEY UPDATE stripe_customer_id = VALUES(stripe_customer_id), stripe_subscription_id = VALUES(stripe_subscription_id), status = VALUES(status), plan = VALUES(plan), current_period_end = VALUES(current_period_end), updated_at = NOW()');
        $stmt->execute([
            $data['workspace_id'],
            $data['stripe_customer_id'],
            $data['stripe_subscription_id'],
            $data['status'],
            $data['plan'],
            $data['current_period_end'],
        ]);
    }
}
