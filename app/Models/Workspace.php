<?php
namespace App\Models;

use App\Core\DB;

class Workspace
{
    public static function create(array $data): int
    {
        $stmt = DB::pdo()->prepare('INSERT INTO workspaces (name, white_label_name, white_label_logo_path, created_by, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())');
        $stmt->execute([
            $data['name'],
            $data['white_label_name'] ?? null,
            $data['white_label_logo_path'] ?? null,
            $data['created_by'],
        ]);
        return (int) DB::pdo()->lastInsertId();
    }

    public static function addMember(int $workspaceId, int $userId, string $role): void
    {
        $stmt = DB::pdo()->prepare('INSERT INTO workspace_members (workspace_id, user_id, role) VALUES (?, ?, ?)');
        $stmt->execute([$workspaceId, $userId, $role]);
    }

    public static function forUser(int $userId): array
    {
        $stmt = DB::pdo()->prepare('SELECT w.*, wm.role FROM workspaces w JOIN workspace_members wm ON wm.workspace_id = w.id WHERE wm.user_id = ? ORDER BY w.created_at DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public static function findById(int $id): ?array
    {
        $stmt = DB::pdo()->prepare('SELECT * FROM workspaces WHERE id = ?');
        $stmt->execute([$id]);
        $workspace = $stmt->fetch();
        return $workspace ?: null;
    }

    public static function roleForUser(int $workspaceId, int $userId): ?string
    {
        $stmt = DB::pdo()->prepare('SELECT role FROM workspace_members WHERE workspace_id = ? AND user_id = ?');
        $stmt->execute([$workspaceId, $userId]);
        $row = $stmt->fetch();
        return $row['role'] ?? null;
    }
}
