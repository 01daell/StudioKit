<?php
namespace App\Models;

use App\Core\DB;

class ShareLink
{
    public static function create(int $brandKitId, string $token): void
    {
        $stmt = DB::pdo()->prepare('INSERT INTO share_links (brand_kit_id, token, created_at) VALUES (?, ?, NOW())');
        $stmt->execute([$brandKitId, $token]);
    }

    public static function findByToken(string $token): ?array
    {
        $stmt = DB::pdo()->prepare('SELECT * FROM share_links WHERE token = ? AND revoked_at IS NULL');
        $stmt->execute([$token]);
        $link = $stmt->fetch();
        return $link ?: null;
    }

    public static function revoke(int $brandKitId): void
    {
        $stmt = DB::pdo()->prepare('UPDATE share_links SET revoked_at = NOW() WHERE brand_kit_id = ? AND revoked_at IS NULL');
        $stmt->execute([$brandKitId]);
    }

    public static function activeForKit(int $brandKitId): ?array
    {
        $stmt = DB::pdo()->prepare('SELECT * FROM share_links WHERE brand_kit_id = ? AND revoked_at IS NULL ORDER BY created_at DESC LIMIT 1');
        $stmt->execute([$brandKitId]);
        $link = $stmt->fetch();
        return $link ?: null;
    }
}
