<?php
use App\Core\Session;
use App\Core\Config;

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    $base = rtrim((string) Config::get('app.url', ''), '/');
    if ($base === '') {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
        if (str_ends_with($scriptDir, '/install')) {
            $scriptDir = rtrim(substr($scriptDir, 0, -strlen('/install')), '/');
        }
        $base = $scheme . '://' . $host . ($scriptDir !== '' ? $scriptDir : '');
    }
    $path = '/' . ltrim($path, '/');
    return $base . $path;
}

function csrf_field(): string
{
    $token = Session::csrfToken();
    return '<input type="hidden" name="_csrf" value="' . h($token) . '">';
}

function config(string $key, $default = null)
{
    return Config::get($key, $default);
}

function redirect(string $path): void
{
    header('Location: ' . url($path));
    exit;
}
