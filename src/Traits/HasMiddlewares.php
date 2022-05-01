<?php

namespace Jedi\Traits;

use InvalidArgumentException;

trait HasMiddlewares
{
    /**
     * Route middlewares.
     *
     * @var callable[]
     */
    protected $middlewares = [];

    /**
     * Register a middleware.
     */
    public function use($middleware): self
    {
        if (\is_array($middleware)) {
            foreach ($middleware as $mid) {
                $this->use($mid);
            }

            return $this;
        }

        if (\is_callable($middleware)) {
            $this->middlewares[] = $middleware;

            return $this;
        }

        throw new InvalidArgumentException('middleware is not of callable type.');
    }

    /**
     * Get the final handler function that will curry the middlewares and main handler.
     */
    public function getFinalHandler(callable $handler): callable
    {
        return array_reduce(
            array_reverse($this->middlewares),
            function (
                $next,
                $middleware
            ) {
                return function ($ctx) use ($next, $middleware) {
                    return call_user_func($middleware, $ctx, $next);
                };
            },
            function (...$args) use ($handler) {
                return \call_user_func_array($handler, $args);
            },
        );
    }
}
