<?php
namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $path, array|callable $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post(string $path, array|callable $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    private function addRoute(string $method, string $path, array|callable $handler, array $middleware): void
    {
        $this->routes[] = [
            'method' => $method,
            'path' => '/' . trim($path, '/'),
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(): void
    {
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $this->resolveUri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if ($route['path'] === $uri) {
                // Execute middleware
                foreach ($route['middleware'] as $mw) {
                    $this->executeMiddleware($mw);
                }

                $handler = $route['handler'];
                if (is_callable($handler)) {
                    call_user_func($handler);
                    return;
                }

                if (is_array($handler) && count($handler) === 2) {
                    [$class, $action] = $handler;
                    $controller = new $class();
                    $controller->$action();
                    return;
                }
            }
        }

        // Route not found
        http_response_code(404);
        if (str_starts_with($uri, '/api/')) {
            Response::json(['error' => 'Endpoint not found', 'path' => $uri], 404);
        } else {
            Response::view('layouts/404', ['uri' => $uri], 'main');
        }
    }

    private function resolveUri(): string
    {
        // Check for manual route param (?r=/admin/dashboard)
        if (isset($_GET['r']) && !empty($_GET['r'])) {
            return '/' . trim($_GET['r'], '/');
        }

        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        
        // Strip query string
        if (false !== ($pos = strpos($uri, '?'))) {
            $uri = substr($uri, 0, $pos);
        }

        // Strip subdirectories if running in subfolder e.g. /waterbillingsystem/public/
        $baseDir = dirname($scriptName);
        if ($baseDir !== '/' && str_starts_with($uri, $baseDir)) {
            $uri = substr($uri, strlen($baseDir));
        }

        $uri = '/' . trim($uri, '/');
        return $uri === '/' ? '/' : rtrim($uri, '/');
    }

    private function executeMiddleware(string $middleware): void
    {
        if ($middleware === 'admin') {
            Auth::requireAdmin();
        } elseif ($middleware === 'tenant') {
            Auth::requireTenant();
        } elseif ($middleware === 'auth') {
            if (!Auth::check()) {
                Response::redirect('/login');
            }
        }
    }
}
