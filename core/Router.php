<?php

namespace Jedi;

class Router
{
    /**
     * The array of registered routes.
     */
    protected array $routes = [];

    /**
     * The Jedi application's request instance.
     */
    protected Request $request;

    /**
     * Creates a new Jedi Router instance.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Register a GET route.
     */
    public function get(string $path, callable $handler): self
    {
        return $this->map('GET', $path, $handler);
    }

    /**
     * Register a route.
     */
    public function map(string $method, string $path, callable $handler): self
    {
        $this->routes[] = [
            'path' => $path,
            'method' => $method,
            'handler' => $handler,
        ];

        return $this;
    }

    /**
     * Execute the registered handler for the current request.
     */
    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();

        foreach ($this->routes as $route) {
            if ($route['path'] === $path && $route['method'] === $method) {
                die(\call_user_func($route['handler']));
            }
        }

        die('Page Not Found');
    }
}
