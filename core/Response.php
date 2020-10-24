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

    public function addHeader(string $str, int $code = \null)
    {
        if (!\headers_sent()) {
            \header($str, true, $code);
        }
    }

    public function redirect(string $url)
    {
        $this->addHeader('Location: ' . $url);
    }

    public function back()
    {
        $ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '#';
        $this->redirect($ref);
    }

    public function type(string $type)
    {
        $this->addHeader('Content-Type: ' . $type);
    }


    public function text(string $text)
    {
        $this->type('text/plain');

        return $text;
    }

    public function json($data)
    {
        $this->type('application/json');

        return \json_encode($data);
    }

    public function jsonp($data, string $func = 'callback')
    {
        if (!isset($_GET[$func])) {
            return 'No JSONP Callback `' . $func . '`';
        }

        $func = $_GET[$func];
        $data = $this->json($data);

        $this->type('text/javascript');

        return '/**/ typeof ' . $func . ' === "function" && ' . $func . '(' . $data  . ');';
    }
}
