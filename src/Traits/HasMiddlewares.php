<?php

namespace Jedi\Traits;

use Jedi\Context\Context;
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
     * Call the handler function but run middlewares first.
     */
    protected function callHandler(Context $context, array $middlewares, callable $handler)
    {
        $handler = array_reduce(
            array_reverse($middlewares),
            function (
                $next,
                $middleware
            ) use ($context) {
                return function () use ($context, $next, $middleware) {
                    return \call_user_func($middleware, $context, $next);
                };
            },
            function () use ($context, $handler) {
                return \call_user_func($handler, $context);
            },
        );

        return \call_user_func($handler);
    }
}
