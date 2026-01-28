<?php
namespace App\Models;

use App\Core\DB;

class PasswordReset
{
    public static function create(string $email, string $token, string $expiresAt): void
    {
        $stmt = DB::pdo()->prepare('INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)');
        $stmt->execute([$email, $token, $expiresAt]);
    }

    public static function findValid(string $token): ?array
    {
        $stmt = DB::pdo()->prepare('SELECT * FROM password_resets WHERE token = ? AND expires_at > NOW()');
        $stmt->execute([$token]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function deleteByToken(string $token): void
    {
        $stmt = DB::pdo()->prepare('DELETE FROM password_resets WHERE token = ?');
        $stmt->execute([$token]);
    }
}
