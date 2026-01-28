<?php
use App\Core\Session;
use App\Core\Config;

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function url(string $path = ''): string
{
    $base = rtrim(Config::get('app.url', ''), '/');
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
