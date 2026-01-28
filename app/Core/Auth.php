<?php
namespace App\Core;

use App\Models\User;

class Auth
{
    public static function user(): ?array
    {
        $id = Session::get('user_id');
        if (!$id) {
            return null;
        }
        return User::findById($id);
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function attempt(string $email, string $password): bool
    {
        $user = User::findByEmail($email);
        if (!$user) {
            return false;
        }
        if (!password_verify($password, $user['password_hash'])) {
            return false;
        }
        Session::set('user_id', $user['id']);
        return true;
    }

    public static function logout(): void
    {
        Session::forget('user_id');
    }
}
