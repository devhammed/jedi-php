<?php

namespace Jedi;

class Application
{
    /**
     * Jedi application's router instance.
     */
    public Router $router;

    /**
     * The Jedi application's request instance.
     */
    protected Request $request;

    /**
     * Creates a new Jedi application.
     */
    public function __construct()
    {
        $this->request = new Request();
        $this->router = new Router($this->request);
    }

    /**
     * Runs the Jedi application.
     */
    public function run()
    {
        $this->router->resolve();
    }
}
