<?php
namespace App\Core;

class Response
{
    public function redirect(string $path): void
    {
        header('Location: ' . url($path));
        exit;
    }

    public function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
