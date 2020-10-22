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
     * Register a GET view route.
     */
    public function getView(string $path, string $view): self
    {
        return $this->view('GET', $path, $view);
    }

    /**
     * Register a view route.
     */
    public function view(string $method, string $path, string $view): self
    {
        return $this->map($method, $path, function () use ($view) {
            return $view;
        });
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
    public function resolve(): string
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();

        foreach ($this->routes as $route) {
            if ($route['path'] === $path && $route['method'] === $method) {
                return \call_user_func($route['handler']);
            }
        }

        return 'Page Not Found';
    }
}
