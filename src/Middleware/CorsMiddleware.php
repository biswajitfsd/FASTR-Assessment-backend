<?php

namespace App\Middleware;

class CorsMiddleware implements MiddlewareInterface
{
    public function handle(callable $next)
    {
        $allowedOrigins = [
            'http://localhost:4200',
            'http://localhost:3000',
            'http://127.0.0.1:4200',
            'http://127.0.0.1:3000',
        ];

        $origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';

        if (in_array($origin, $allowedOrigins)) {
            header("Access-Control-Allow-Origin: $origin");
            header('Access-Control-Allow-Credentials: true');
        } else {
            header("Access-Control-Allow-Origin: *");
        }

        header('Access-Control-Max-Age: 86400');    // cache for 1 day
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE, PATCH");
        header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            header("HTTP/1.1 204 NO CONTENT");
            exit();
        }

        return $next();
    }
}