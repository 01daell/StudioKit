<?php
namespace App\Models;

use App\Core\DB;

class FontSelection
{
    public static function upsert(int $kitId, array $data): void
    {
        $stmt = DB::pdo()->prepare('INSERT INTO font_selections (brand_kit_id, heading_font, body_font, heading_weights, body_weights, source, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())
            ON DUPLICATE KEY UPDATE heading_font = VALUES(heading_font), body_font = VALUES(body_font), heading_weights = VALUES(heading_weights), body_weights = VALUES(body_weights), source = VALUES(source), updated_at = NOW()');
        $stmt->execute([
            $kitId,
            $data['heading_font'],
            $data['body_font'],
            $data['heading_weights'],
            $data['body_weights'],
            $data['source'],
        ]);
    }

    public static function forKit(int $kitId): ?array
    {
        $stmt = DB::pdo()->prepare('SELECT * FROM font_selections WHERE brand_kit_id = ?');
        $stmt->execute([$kitId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
