<?php

namespace Jedi;

class Response
{
    /**
     * HTTP Status Codes.
     */
    public const HTTP_OK = 200;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_INTERNAL_SERVER_ERROR = 500;

    /**
     * Set response status code.
     */
    public function setStatus(int $code)
    {
        \http_response_code($code);
    }
}
