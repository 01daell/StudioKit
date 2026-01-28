<?php
namespace App\Core;

class Request
{
    public string $method;
    public string $path;
    public array $query;
    public array $body;
    public array $files;

    public static function capture(): self
    {
        $instance = new self();
        $instance->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $instance->path = parse_url($uri, PHP_URL_PATH) ?? '/';
        $instance->query = $_GET;
        $instance->body = $_POST;
        $instance->files = $_FILES;
        return $instance;
    }

    public function input(string $key, $default = null)
    {
        return $this->body[$key] ?? $default;
    }
}
