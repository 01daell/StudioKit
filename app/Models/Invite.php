<?php
namespace App\Models;

use App\Core\DB;

class Invite
{
    public static function create(array $data): void
    {
        $stmt = DB::pdo()->prepare('INSERT INTO invites (workspace_id, email, role, token, status, expires_at, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())');
        $stmt->execute([
            $data['workspace_id'],
            $data['email'],
            $data['role'],
            $data['token'],
            $data['status'],
            $data['expires_at'],
        ]);
    }

    public static function forWorkspace(int $workspaceId): array
    {
        $stmt = DB::pdo()->prepare('SELECT * FROM invites WHERE workspace_id = ? ORDER BY created_at DESC');
        $stmt->execute([$workspaceId]);
        return $stmt->fetchAll();
    }
}
