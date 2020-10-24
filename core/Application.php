<?php

namespace Jedi;

use Closure;
use Throwable;
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
     * The router's error handler.
     */
    protected Closure $error;

    /**
     * Creates a new Jedi application.
     */
    public function __construct()
    {
        $this->context = new Context($this);
        $this->fallback =  fn () => 'Page Not Found.';
        $this->error = fn (Throwable $e) => 'Something bad just happened: ' .
            $e->getMessage();
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
     * Register a custom error handler.
     */
    public function error(Closure $error): self
    {
        $this->error = $error;

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
     * Register a GET view route.
     */
    public function getView(string $path, string $view): Route
    {
        return $this->view('GET', $path, $view);
    }

    /**
     * Register a POST route.
     */
    public function post(string $path, callable $handler): Route
    {
        return $this->map('POST', $path, $handler);
    }

    /**
     * Register a POST view route.
     */
    public function postView(string $path, string $view): Route
    {
        return $this->view('POST', $path, $view);
    }

    /**
     * Register a view route.
     */
    public function view(string $method, string $path, string $view): Route
    {
        return $this->map($method, $path, function () use ($view) {
            return $view;
        });
    }

    /**
     * Register a route.
     */
    public function map(string $method, string $path, callable $handler): Route
    {
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
        try {
            echo $this->handleRequest();
        } catch (Throwable $e) {
            $this->context
                ->response
                ->setStatus($this->context->response::HTTP_INTERNAL_SERVER_ERROR);

            return \call_user_func($this->error, $e);
        }
    }

    /**
     * Execute the registered handler for the current request.
     */
    protected function handleRequest()
    {
        $requestPath = $this->context->request->getPath();
        $requestMethod = $this->context->request->getMethod();

        foreach ($this->routes as $route) {
            if (
                \preg_match($route->getPath(), $requestPath, $args) &&
                \in_array($requestMethod, $route->getMethods())
            ) {
                \array_shift($args);

                $this->context->args->setArgs($args);

                return \call_user_func(
                    $this->getFinalHandler(
                        $route->getMiddlewares(),
                        $route->getHandler(),
                    ),
                    $this->context,
                );
            }
        }

        $this->context
            ->response
            ->setStatus($this->context->response::HTTP_NOT_FOUND);

        return \call_user_func($this->fallback);
    }
}
