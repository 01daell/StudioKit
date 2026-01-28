<?php
namespace App\Models;

use App\Core\DB;

class BrandAsset
{
    public static function create(array $data): int
    {
        $stmt = DB::pdo()->prepare('INSERT INTO brand_assets (brand_kit_id, type, path, mime, size, original_name, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute([
            $data['brand_kit_id'],
            $data['type'],
            $data['path'],
            $data['mime'],
            $data['size'],
            $data['original_name'],
        ]);
        return (int) DB::pdo()->lastInsertId();
    }

    public static function updateType(int $id, string $type): void
    {
        $stmt = DB::pdo()->prepare('UPDATE brand_assets SET type = ? WHERE id = ?');
        $stmt->execute([$type, $id]);
    }

    public static function delete(int $id): void
    {
        $stmt = DB::pdo()->prepare('DELETE FROM brand_assets WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function forKit(int $kitId): array
    {
        $stmt = DB::pdo()->prepare('SELECT * FROM brand_assets WHERE brand_kit_id = ? ORDER BY created_at DESC');
        $stmt->execute([$kitId]);
        return $stmt->fetchAll();
    }
}
