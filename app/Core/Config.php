<?php
namespace App\Core;

class Config
{
    private static array $data = [];

    public static function load(string $path): void
    {
        if (file_exists($path)) {
            $config = require $path;
            if (is_array($config)) {
                self::$data = $config;
            }
        }
    }

    public static function get(string $key, $default = null)
    {
        $segments = explode('.', $key);
        $value = self::$data;
        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }
            $value = $value[$segment];
        }
        return $value;
    }

    public static function set(string $key, $value): void
    {
        $segments = explode('.', $key);
        $ref = &self::$data;
        foreach ($segments as $segment) {
            if (!isset($ref[$segment]) || !is_array($ref[$segment])) {
                $ref[$segment] = [];
            }
            $ref = &$ref[$segment];
        }
        $ref = $value;
    }
}
