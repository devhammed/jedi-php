<?php

namespace Jedi;

class Request
{
    /**
     * Internal variables.
     */
    protected array $get;
    protected array $post;
    protected array $server;
    protected array $files;
    protected array $headers;

    /**
     * Creates a new Jedi Request instance.
     */
    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
        $this->server = $_SERVER;
        $this->headers = $this->getRequestHeaders();
    }

    /**
     * Returns GET value.
     */
    public function get(string $key, $def = \null): ?string
    {
        return isset($this->get[$key]) ? $this->get[$key] : $def;
    }

    /**
     * Returns POST value.
     */
    public function post(string $key, $def = \null): ?string
    {
        return isset($this->post[$key]) ? $this->post[$key] : $def;
    }

    /**
     * Returns POST value.
     */
    public function server(string $key, ?string $def = \null): ?string
    {
        $key = strtoupper($key);

        return isset($this->server[$key]) ? $this->server[$key] : $def;
    }

    /**
     * Get the current request URI.
     *
     * @return string
     */
    public function path(): string
    {
        return \strtok($_SERVER['REQUEST_URI'], '?');
    }

    /**
     * Get the current request URL.
     */
    public function url(?string $str = \null): string
    {
        $protocol = !empty($this->server['HTTPS']) && $this->server['HTTPS'] === 'on'
            ? 'https://'
            : 'http://';

        return $protocol . $this->server['HTTP_HOST'] . $str;
    }

    /**
     * Get the current request method.
     */
    public function method(?string $type = \null): string
    {
        $verb = \strtoupper($this->server('REQUEST_METHOD'));

        if (
            $this->hasHeader('X-Http-Method-Override')
        ) {
            $verb = \strtoupper($this->header('X-Http-Method-Override'));
        } else {
            $verb = isset($this->post['_method'])
                ? \strtoupper($this->post['_method'])
                : $verb;
        }

        return \is_null($type) ? $verb : (\strtoupper($type) === $verb);
    }

    /**
     * Check if request has header.
     */
    public function hasHeader(string $key): bool
    {
        return isset($this->headers[\strtoupper($key)]);
    }

    /**
     * Get a request header.
     */
    public function header(string $key, ?string $def = \null): ?string
    {
        return $this->hasHeader($key) ? $this->headers[\strtoupper($key)] : $def;
    }

    /**
     * Get all request headers.
     */
    public function headers(): array
    {
        return $this->headers;
    }

    /**
     * Get all request headers from $_SERVER variables.
     */
    protected function getRequestHeaders(): array
    {
        $headers = [];

        foreach ($this->server as $k => $v) {
            if (\substr($k, 0, 5) == 'HTTP_') {
                $k = \str_replace('_', '-', \substr($k, 5));
                $headers[$k] = $v;
            } elseif ($k === 'CONTENT_TYPE' || $k === 'CONTENT_LENGTH') {
                $k = \str_replace('_', '-', $k);
                $headers[$k] = $v;
            }
        }

        return $headers;
    }
}
