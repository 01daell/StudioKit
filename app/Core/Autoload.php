<?php
namespace App\Core;

class Autoload
{
    public static function register(): void
    {
        spl_autoload_register(function (string $class): void {
            if (str_starts_with($class, 'App\\')) {
                $path = __DIR__ . '/../' . str_replace('App\\', '', $class) . '.php';
                $path = str_replace('\\', '/', $path);
                if (file_exists($path)) {
                    require_once $path;
                }
            }
        });
    }
}
