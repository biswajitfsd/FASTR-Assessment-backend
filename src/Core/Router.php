<?php

namespace App\Core;

use App\Middleware\MiddlewareInterface;

class Router
{
    protected $routes = [];
    protected $globalMiddlewares = [];

    public function get($path, $callback)
    {
        $this->addRoute('GET', $path, $callback);
    }

    public function post($path, $callback)
    {
        $this->addRoute('POST', $path, $callback);
    }

    public function options($path, $callback)
    {
        $this->addRoute('OPTIONS', $path, $callback);
    }

    public function addRoute($method, $path, $callback)
    {
        $this->routes[$method][$path] = ['callback' => $callback, 'middlewares' => []];
    }

    public function addMiddleware($path, MiddlewareInterface $middleware)
    {
        foreach ($this->routes as $method => $routes) {
            if (isset($routes[$path])) {
                $this->routes[$method][$path]['middlewares'][] = $middleware;
            }
        }
    }

    public function addGlobalMiddleware(MiddlewareInterface $middleware): void
    {
        $this->globalMiddlewares[] = $middleware;
    }

    public function dispatch()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $route = $this->routes[$method][$path] ?? false;

        $stack = new MiddlewareStack(function () use ($route, $method, $path) {
            if ($route === false) {
                return ['error' => 'Not Found'];
            }

            if ($method === 'OPTIONS') {
                return ['message' => 'OK']; // Respond to preflight requests
            }

            return $this->runCallback($route['callback']);
        });

        foreach ($this->globalMiddlewares as $middleware) {
            $stack->add($middleware);
        }

        if ($route) {
            foreach ($route['middlewares'] as $middleware) {
                $stack->add($middleware);
            }
        }

        try {
            $result = $stack->handle();
            $this->sendJsonResponse($result);
        } catch (\Exception $e) {
            $this->sendJsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    protected function runCallback($callback)
    {
        if (is_callable($callback)) {
            return call_user_func($callback);
        }

        if (is_string($callback)) {
            list($controller, $method) = explode('@', $callback);
            $controllerClass = "App\\Controllers\\$controller";
            if (class_exists($controllerClass)) {
                $controllerInstance = new $controllerClass();
                if (method_exists($controllerInstance, $method)) {
                    return call_user_func([$controllerInstance, $method]);
                }
            }
        }

        throw new \RuntimeException("Invalid callback: $callback");
    }

    protected function sendJsonResponse($data, $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
    }
}