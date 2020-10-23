<?php

namespace Jedi;

class Application
{
    /**
     * Jedi application's router instance.
     */
    public Router $router;

    /**
     * Jedi application's context instance.
     */
    protected Context $context;

    /**
     * Creates a new Jedi application.
     */
    public function __construct()
    {
        $this->context = new Context();
        $this->router = $this->context->router;
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
     * Runs the Jedi application.
     */
    public function run()
    {
        echo $this->router->resolve();
    }
}
