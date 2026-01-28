<?php
namespace App\Core;

class RateLimiter
{
    public static function hit(string $key, int $max, int $windowSeconds): bool
    {
        $now = time();
        $bucket = Session::get('_rate_' . $key, []);
        $bucket = array_filter($bucket, fn($ts) => $ts > $now - $windowSeconds);
        if (count($bucket) >= $max) {
            Session::set('_rate_' . $key, $bucket);
            return false;
        }
        $bucket[] = $now;
        Session::set('_rate_' . $key, $bucket);
        return true;
    }
}
