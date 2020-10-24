<?php

namespace Jedi;

use Throwable;
use Jedi\Response\TransformedResponse;

class Response
{
    /**
     * HTTP Status Codes.
     */
    public const HTTP_OK = 200;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_INTERNAL_SERVER_ERROR = 500;

    /**
     * Set a response header.
     */
    public function header(string $key, string $value, bool $replace = \true): self
    {
        if (!\headers_sent()) {
            \header($key . ': ' . $value, $replace);
        }

        return $this;
    }

    /**
     * Send a redirect response.
     */
    public function redirect(string $url): self
    {
        $this->header('Location', $url);

        return $this;
    }

    /**
     * Go back to the referring page.
     */
    public function back(): self
    {
        $this->redirect(
            isset($_SERVER['HTTP_REFERER'])
                ? $_SERVER['HTTP_REFERER']
                : '#'
        );

        return $this;
    }

    /**
     * Set response status code.
     */
    public function status(int $code): self
    {
        \http_response_code($code);

        return $this;
    }

    /**
     * Set the response type.
     */
    public function type(string $type): self
    {
        $this->header('Content-Type', $type);

        return $this;
    }

    /**
     * Send a generic response.
     */
    public function send($response): TransformedResponse
    {
        try {
            // Check if data has been transformed...
            if ($response instanceof TransformedResponse) {
                return $response;
            }

            // Handle arrays...
            if (\is_array($response)) {
                return $this->json($response);
            }

            // Handle plain types like strings, numbers, floats etc.
            if (!\preg_match('~<\/?[a-z][\s\S]*>~', $response)) {
                return $this->text($response);
            }

            // Handle HTML, binary file etc.
            return new TransformedResponse($response);
        } catch (Throwable $e) {
            // The flow will only get here if:
            //    1. preg_match fails
            // it is 100% safe to just return the response:

            return new TransformedResponse($response);
        }
    }

    /**
     * Send a plain text response.
     */
    public function text($text): TransformedResponse
    {
        $this->type('text/plain');

        return new TransformedResponse($text);
    }

    /**
     * Send a JSON response.
     */
    public function json($data): TransformedResponse
    {
        $this->type('application/json');

        return new TransformedResponse(\json_encode($data));
    }

    /**
     * Send a JSONP response.
     */
    public function jsonp($data, string $func = 'callback'): TransformedResponse
    {
        if (!isset($_GET[$func])) {
            return 'No JSONP Callback `' . $func . '`';
        }

        $func = $_GET[$func];
        $data = $this->json($data);

        $this->type('text/javascript');

        return new TransformedResponse('/**/ typeof ' . $func . ' === "function" && ' . $func . '(' . $data  . ');');
    }
}
