<?php
namespace App\Models;

use App\Core\DB;

class Color
{
    public static function replaceForKit(int $kitId, array $colors): void
    {
        $delete = DB::pdo()->prepare('DELETE FROM colors WHERE brand_kit_id = ?');
        $delete->execute([$kitId]);
        $stmt = DB::pdo()->prepare('INSERT INTO colors (brand_kit_id, name, hex, sort_order, locked, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
        foreach ($colors as $index => $color) {
            $stmt->execute([
                $kitId,
                $color['name'],
                $color['hex'],
                $index,
                $color['locked'] ? 1 : 0,
            ]);
        }
    }

    public static function forKit(int $kitId): array
    {
        $stmt = DB::pdo()->prepare('SELECT * FROM colors WHERE brand_kit_id = ? ORDER BY sort_order ASC');
        $stmt->execute([$kitId]);
        return $stmt->fetchAll();
    }
}
