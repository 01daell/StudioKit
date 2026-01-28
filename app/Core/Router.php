<?php
namespace App\Core;

class Router
{
    private Request $request;
    private Response $response;
    private array $routes = [];

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function get(string $path, callable|array $handler): void
    {
        $this->map('GET', $path, $handler);
    }

    public function post(string $path, callable|array $handler): void
    {
        $this->map('POST', $path, $handler);
    }

    private function map(string $method, string $path, callable|array $handler): void
    {
        $this->routes[] = compact('method', 'path', 'handler');
    }

    public function dispatch(): void
    {
        foreach ($this->routes as $route) {
            if ($route['method'] !== $this->request->method) {
                continue;
            }
            $pattern = preg_replace('#\{[^/]+\}#', '([^/]+)', $route['path']);
            $pattern = '#^' . $pattern . '$#';
            if (preg_match($pattern, $this->request->path, $matches)) {
                array_shift($matches);
                $handler = $route['handler'];
                if (is_array($handler)) {
                    [$class, $method] = $handler;
                    $instance = new $class($this->request, $this->response);
                    call_user_func_array([$instance, $method], $matches);
                    return;
                }
                call_user_func_array($handler, $matches);
                return;
            }
        }
        http_response_code(404);
        echo '404 Not Found';
    }
}
