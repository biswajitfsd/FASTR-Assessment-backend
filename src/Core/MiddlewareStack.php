<?php

namespace App\Core;

use App\Middleware\MiddlewareInterface;

class MiddlewareStack
{
    protected array $middlewares = [];
    protected $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function add(MiddlewareInterface $middleware): static
    {
        $this->middlewares[] = $middleware;
        return $this;
    }

    public function handle()
    {
        $callback = $this->callback;
        $middlewares = $this->middlewares;

        $run = function () use (&$middlewares, &$run, $callback) {
            if (empty($middlewares)) {
                return $callback();
            }

            $middleware = array_shift($middlewares);
            return $middleware->handle($run);
        };

        return $run();
    }
}