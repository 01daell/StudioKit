<?php
namespace App\Core;

class Installer
{
    public static function isInstalled(): bool
    {
        return file_exists(__DIR__ . '/../../storage/installed.lock');
    }

    public static function guard(Request $request, Response $response): void
    {
        $isInstallRoute = str_starts_with($request->path, '/install');
        if (!self::isInstalled() && !$isInstallRoute) {
            $response->redirect('/install');
        }
        if (self::isInstalled() && $isInstallRoute) {
            $response->redirect('/sign-in');
        }
    }
}
