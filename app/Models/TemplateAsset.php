<?php
namespace App\Models;

use App\Core\DB;

class TemplateAsset
{
    public static function create(array $data): void
    {
        $stmt = DB::pdo()->prepare('INSERT INTO template_assets (brand_kit_id, type, path, meta, created_at) VALUES (?, ?, ?, ?, NOW())');
        $stmt->execute([
            $data['brand_kit_id'],
            $data['type'],
            $data['path'],
            $data['meta'],
        ]);
    }

    public static function forKit(int $kitId): array
    {
        $stmt = DB::pdo()->prepare('SELECT * FROM template_assets WHERE brand_kit_id = ? ORDER BY created_at DESC');
        $stmt->execute([$kitId]);
        return $stmt->fetchAll();
    }
}
