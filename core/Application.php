<?php

namespace Jedi;

class Application
{
    /**
     * Jedi application's router instance.
     */
    public Router $router;

    /**
     * Jedi application's request instance.
     */
    protected Request $request;

    /**
     * Jedi application's response instance.
     */
    protected Response $response;

    /**
     * Creates a new Jedi application.
     */
    public function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
    }

    /**
     * Runs the Jedi application.
     */
    public function run()
    {
        echo $this->router->resolve();
    }
}
