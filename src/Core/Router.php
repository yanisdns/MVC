<?php

namespace App\Core;

use App\Controllers\ErrorController;

class Router
{
    private array $routes = ['GET' => [], 'POST' => []];

    public function get(string $path, array $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, array $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $base = base_path();
        if ($base !== '' && strncmp($path, $base, strlen($base)) === 0) {
            $path = substr($path, strlen($base));
        }
        $path = '/' . trim($path, '/');

        foreach ($this->routes[$method] ?? [] as $pattern => $handler) {
            $regex = '#^' . preg_replace('#\{(\w+)\}#', '(?P<$1>[^/]+)', $pattern) . '$#';
            if (preg_match($regex, $path, $matches)) {
                $params = array_values(array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY));
                [$class, $action] = $handler;
                $controller = new $class();
                $controller->$action(...$params);
                return;
            }
        }

        (new ErrorController())->notFound();
    }
}
