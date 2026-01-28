<?php
namespace App\Models;

use App\Core\DB;

class BrandKit
{
    public static function create(array $data): int
    {
        $stmt = DB::pdo()->prepare('INSERT INTO brand_kits (workspace_id, name, tagline, description, voice_keywords, usage_do, usage_dont, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
        $stmt->execute([
            $data['workspace_id'],
            $data['name'],
            $data['tagline'],
            $data['description'],
            $data['voice_keywords'],
            $data['usage_do'],
            $data['usage_dont'],
        ]);
        return (int) DB::pdo()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $stmt = DB::pdo()->prepare('UPDATE brand_kits SET name = ?, tagline = ?, description = ?, voice_keywords = ?, usage_do = ?, usage_dont = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([
            $data['name'],
            $data['tagline'],
            $data['description'],
            $data['voice_keywords'],
            $data['usage_do'],
            $data['usage_dont'],
            $id,
        ]);
    }

    public static function delete(int $id): void
    {
        $stmt = DB::pdo()->prepare('DELETE FROM brand_kits WHERE id = ?');
        $stmt->execute([$id]);
    }

    public static function findById(int $id): ?array
    {
        $stmt = DB::pdo()->prepare('SELECT * FROM brand_kits WHERE id = ?');
        $stmt->execute([$id]);
        $kit = $stmt->fetch();
        return $kit ?: null;
    }

    public static function forWorkspace(int $workspaceId): array
    {
        $stmt = DB::pdo()->prepare('SELECT * FROM brand_kits WHERE workspace_id = ? ORDER BY created_at DESC');
        $stmt->execute([$workspaceId]);
        return $stmt->fetchAll();
    }
}
