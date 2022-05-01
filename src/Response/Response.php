<?php

namespace Jedi\Response;

use Throwable;

class Response
{
    /**
     * HTTP Status Codes.
     */
    public const HTTP_OK                       = 200;
    public const HTTP_CREATED                  = 201;
    public const HTTP_ACCEPTED                 = 202;
    public const HTTP_NO_CONTENT               = 204;
    public const HTTP_MOVED_PERMANENTLY        = 301;
    public const HTTP_FOUND                    = 302;
    public const HTTP_SEE_OTHER                = 303;
    public const HTTP_NOT_MODIFIED             = 304;
    public const HTTP_TEMPORARY_REDIRECT       = 307;
    public const HTTP_PERMANENT_REDIRECT       = 308;
    public const HTTP_BAD_REQUEST              = 400;
    public const HTTP_UNAUTHORIZED             = 401;
    public const HTTP_FORBIDDEN                = 403;
    public const HTTP_NOT_FOUND                = 404;
    public const HTTP_METHOD_NOT_ALLOWED       = 405;
    public const HTTP_NOT_ACCEPTABLE           = 406;
    public const HTTP_REQUEST_TIMEOUT          = 408;
    public const HTTP_CONFLICT                 = 409;
    public const HTTP_GONE                     = 410;
    public const HTTP_LENGTH_REQUIRED          = 411;
    public const HTTP_PRECONDITION_FAILED      = 412;
    public const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;
    public const HTTP_REQUEST_URI_TOO_LONG     = 414;
    public const HTTP_UNSUPPORTED_MEDIA_TYPE   = 415;
    public const HTTP_I_AM_A_TEAPOT            = 418;
    public const HTTP_UNPROCESSABLE_ENTITY     = 422;
    public const HTTP_INTERNAL_SERVER_ERROR    = 500;
    public const HTTP_NOT_IMPLEMENTED          = 501;
    public const HTTP_BAD_GATEWAY              = 502;
    public const HTTP_SERVICE_UNAVAILABLE      = 503;
    public const HTTP_GATEWAY_TIMEOUT          = 504;

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
        return $this->header('Location', $url);
    }

    /**
     * Go back to the referring page.
     */
    public function back(): self
    {
        return $this->redirect(
            isset($_SERVER['HTTP_REFERER'])
                ? $_SERVER['HTTP_REFERER']
                : '#'
        );
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
    public function send($response, int $status = \null): TransformedResponse
    {
        if ($status !== \null) {
            $this->status($status);
        }

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
            $this->status(self::HTTP_BAD_REQUEST);

            return '';
        }

        $func = $_GET[$func];
        $data = $this->json($data);

        $this->type('text/javascript');

        // the /**/ is a specific security mitigation for "Rosetta Flash JSONP abuse"
        // the typeof check is just to reduce client error noise
        return new TransformedResponse(
            '/**/ typeof ' . $func . ' === "function" && ' . $func . '(' . $data . ');'
        );
    }
}
