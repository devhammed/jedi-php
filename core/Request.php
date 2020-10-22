<?php

namespace Jedi;

class Request
{
    /**
     * Get the current request URI.
     *
     * @return string
     */
    public function getPath(): string
    {
        return \strtok($_SERVER['REQUEST_URI'], '?');
    }

    /**
     * Get the current request method.
     *
     * @return string
     */
    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }
}
