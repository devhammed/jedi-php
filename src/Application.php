<?php

namespace Jedi;

use Closure;
use Jedi\Traits\HasMiddlewares;

class Application
{
    use HasMiddlewares;

    /**
     * Array of registered routes.
     *
     * @var \Jedi\Route[] $routes
     */
    protected array $routes = [];

    /**
     * Jedi application's context instance.
     */
    protected Context $context;

    /**
     * The router's not found handler.
     */
    protected Closure $fallback;

    /**
     * The routes base path.
     */
    protected string $base = '';

    /**
     * Creates a new Jedi application.
     */
    public function __construct()
    {
        $this->context  = new Context($this);
        $this->fallback =  fn () => 'Page Not Found.';
    }

    /**
     * Register application service.
     */
    public function service(string $name, $value): self
    {
        $this->context[$name] = $value;

        return $this;
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
    public function get(string $path, callable $handler): Route
    {
        return $this->map('GET', $path, $handler);
    }

    /**
     * Register a POST route.
     */
    public function post(string $path, callable $handler): Route
    {
        return $this->map('POST', $path, $handler);
    }

    /**
     * Create a route group.
     */
    public function group(string $base, callable $registrar)
    {
        $oldBase        = $this->base;
        $oldMiddlewares = $this->middlewares;

        $this->base = $oldBase . $base;

        \call_user_func($registrar, $this);

        $this->base        = $oldBase;
        $this->middlewares = $oldMiddlewares;
    }

    /**
     * Register a route.
     */
    public function map(string $method, string $path, callable $handler): Route
    {
        $path = $this->base . ($path === '/' ? '' : $path);

        $method = $method === 'GET' ? ['GET', 'HEAD'] : $method;

        $route = new Route($method, $path, $handler);

        $route->use($this->middlewares);

        $this->routes[] = $route;

        return $route;
    }

    /**
     * Runs the Jedi application.
     */
    public function run()
    {
        echo $this->context->response->send($this->handleRequest()));
    }

    /**
     * Execute the registered handler for the current request.
     */
    protected function handleRequest()
    {
        $requestPath   = $this->context->request->uri();
        $requestMethod = $this->context->request->method();

        foreach ($this->routes as $route) {
            $routeMethods  = $route->getMethods();
            $matchedMethod = \in_array('ANY', $routeMethods) || \in_array($requestMethod, $routeMethods);

            if (
                $matchedMethod &&
                \preg_match($route->getPath(), $requestPath, $args)
            ) {
                \array_shift($args);

                $this->context->request->populateParams($args);

                return \call_user_func(
                    $route->getFinalHandler($route->getHandler()),
                    $this->context,
                );
            }
        }

        $this->context
            ->response
            ->status($this->context->response::HTTP_NOT_FOUND);

        return \call_user_func($this->fallback);
    }
}
