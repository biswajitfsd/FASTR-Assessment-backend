<?php

namespace App\Middleware;

class JsonMiddleware implements MiddlewareInterface
{
    public function handle(callable $next)
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (str_contains($contentType, 'application/json')) {
            $jsonData = file_get_contents('php://input');
            $_POST = json_decode($jsonData, true) ?? [];
        }
        return $next();
    }
}