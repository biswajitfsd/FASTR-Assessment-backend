<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;
use App\Middleware\CorsMiddleware;
use App\Middleware\JsonMiddleware;

$router = new Router();

// Global Middleware
$router->addGlobalMiddleware(new CorsMiddleware());
$router->addGlobalMiddleware(new JsonMiddleware());

// Routes
$router->post('/api/users', 'UserController@store');

$router->dispatch();