<?php

namespace Jedi;

use Closure;

class Router
{
    /**
     * Array of registered routes.
     */
    protected array $routes = [];

    /**
     *  Jedi application's request instance.
     */
    protected Request $request;

    /**
     * Jedi application's response instance.
     */
    protected Response $response;

    /**
     * The route not found handler.
     */
    protected Closure $fallback;

    /**
     * Creates a new Jedi Router instance.
     */
    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
        $this->fallback =  fn () => 'Page Not Found';
    }

    /**
     * Register a custom not found handler.
     */
    public function fallback(Closure $fallback): self
    {
        $this->fallback = $fallback;

        return $this;
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
     * Register a POST route.
     */
    public function post(string $path, callable $handler): self
    {
        return $this->map('POST', $path, $handler);
    }

    /**
     * Register a POST view route.
     */
    public function postView(string $path, string $view): self
    {
        return $this->view('POST', $path, $view);
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

        $this->response->setStatus($this->response::HTTP_NOT_FOUND);

        return \call_user_func($this->fallback);
    }
}
