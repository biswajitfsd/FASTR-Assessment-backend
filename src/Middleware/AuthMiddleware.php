<?php

namespace App\Middleware;

class AuthMiddleware implements MiddlewareInterface
{
    public function handle(callable $next)
    {
        // Check for authentication token
        $token = $_SERVER['HTTP_AUTHORIZATION'] ?? null;
        if (!$token) {
            header('HTTP/1.0 401 Unauthorized');
            echo json_encode(['error' => 'Authentication required']);
            exit;
        }

        return $next();
    }
}