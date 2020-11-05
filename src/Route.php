<?php

namespace Jedi;

use Jedi\Traits\HasMiddlewares;

class Route
{
    use HasMiddlewares;

    /**
     * Route path.
     */
    protected string $path;

    /**
     * Route methods
     *
     * @var string[]
     */
    protected array $methods;

    /**
     * Route handler.
     *
     * @var callable
     */
    protected $handler;

    /**
     * Creates a new Jedi Route instance.
     */
    public function __construct(string $method, string $path, callable $handler)
    {
        $this->path = \preg_replace_callback(
            '#:([\w]+)(\(([^/()]*)\))?#',
            function ($matches) {
                return isset($matches[3])
                    ? '(?P<' . $matches[1] . '>' . $matches[3] . ')'
                    : '(?P<' . $matches[1] . '>[^/]+)';
            },
            '~^' . $path . '/?$~',
        );

        $this->methods = \array_map(
            'trim',
            \array_map(
                'strtoupper',
                \explode('|', $method),
            ),
        );

        $this->handler = $handler;
    }

    /**
     * Get route path.
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Get route methods.
     */
    public function getMethods(): array
    {
        return $this->methods;
    }

    /**
     * Get route handler.
     */
    public function getHandler(): callable
    {
        return $this->handler;
    }

    /**
     * Get route middlewares.
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}
