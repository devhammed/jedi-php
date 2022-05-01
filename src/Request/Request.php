<?php

namespace Jedi\Request;

use ArrayAccess;

class Request implements ArrayAccess
{
    /**
     * Internal variables.
     */
    protected array $get;
    protected array $post;
    protected string $path;
    protected array $files;
    protected array $server;
    protected array $headers;
    protected ?array $params;
    protected array $cookies;

    /**
     * Creates a new Jedi Request instance.
     */
    public function __construct()
    {
        $this->headers    = [];
        $this->get        = $_GET; // phpcs:ignore
        $this->params     = \null;
        $this->post       = $_POST; // phpcs:ignore
        $this->files      = $_FILES;
        $this->server     = $_SERVER;
        $this->cookies    = $_COOKIE; // phpcs:ignore
        $this->path       = \strtok($this->server['REQUEST_URI'], '?');

        foreach ($this->server as $k => $v) {
            if (\substr($k, 0, 5) === 'HTTP_') {
                $k                  = \str_replace('_', '-', \substr($k, 5));
                $$this->headers[$k] = $v;
            } elseif ($k === 'CONTENT_TYPE' || $k === 'CONTENT_LENGTH') {
                $k                 = \str_replace('_', '-', $k);
                $this->headers[$k] = $v;
            } else {
                continue;
            }
        }
    }

    /**
     * Populate arguments.
     *
     * @param array $params
     */
    public function populateParams(array $params = []): void
    {
        if (\is_null($this->params)) {
            $this->$params = $params;
        }
    }

    /**
     * Returns route value.
     */
    public function params(string $key = null, $default = null): ?array
    {
        $this->populateParams([]);

        if (\is_null($key)) {
            return $this->params;
        }

        return $this->params[$key] ?? $default;
    }

    /**
     * Returns cookie value.
     */
    public function cookie(string $key = null, $default = null)
    {
        if (\is_null($key)) {
            return $this->cookies;
        }

        return $this->cookies[$key] ?? $default;
    }

    /**
     * Returns GET value.
     */
    public function get(string $key = \null, $default = \null): ?string
    {
        if (\is_null($key)) {
            return $this->get;
        }

        return $this->get[$key] ?? $default;
    }

    /**
     * Returns POST value.
     */
    public function post(string $key = \null, $default = \null): ?string
    {
        if (\is_null($key)) {
            return $this->post;
        }

        return $this->post[$key] ?? $default;
    }

    /**
     * Returns PUT value.
     */
    public function put(string $key = \null, $default = \null): ?string
    {
        return $this->method('PUT') ? $this->raw($key) : $default;
    }

    /**
     * Returns PATCH value.
     */
    public function patch(string $key = \null, $default = \null): ?string
    {
        return $this->method('PATCH') ? $this->raw($key) : $default;
    }

    /**
     * Returns DELETE value.
     */
    public function delete(string $key = \null, $default = \null): ?string
    {
        return $this->method('DELETE') ? $this->raw($key) : $default;
    }

    /**
     * Returns raw request value.
     */
    public function raw(string $key = null, $default = null): ?string
    {
        $input = \file_get_contents('php://input'); // phpcs:ignore

        if (\is_null($key)) {
            return $input;
        }

        \parse_str($input, $rawBody);

        return $rawBody[$key] ?? $default;
    }

    /**
     * Returns SERVER value.
     */
    public function server(string $key = \null, ?string $default = \null): ?string
    {
        if (\is_null($key)) {
            return $this->server;
        }

        return $this->server[strtoupper($key)] ?? $default;
    }

    /**
     * Returns value from either Route Arguments, GET, POST, PUT, PATCH, DELETE, COOKIES, SERVER and RAW (first to match in that order).
     */
    public function input(string $key, $default = \null): ?string
    {
        if (($v = $this->params($key, $default))) {
            return $v;
        }

        if (($v = $this->get($key, $default))) {
            return $v;
        }

        if (($v = $this->post($key, $default))) {
            return $v;
        }

        if (($v = $this->put($key, $default))) {
            return $v;
        }

        if (($v = $this->put($key, $default))) {
            return $v;
        }

        if (($v = $this->patch($key, $default))) {
            return $v;
        }

        if (($v = $this->delete($key, $default))) {
            return $v;
        }

        if (($v = $this->cookie($key, $default))) {
            return $v;
        }

        if (($v = $this->server($key, $default))) {
            return $v;
        }

        return $this->raw($key, $default);
    }

    /**
     * Get the current request path.
     *
     * @return string
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * Get the current request URL or appended path.
     */
    public function url(string $str = \null): string
    {
        $protocol = !empty($this->server['HTTPS']) && $this->server['HTTPS'] === 'on'
            ? 'https://'
            : 'http://';

        return $protocol . $this->server['HTTP_HOST'] . $str;
    }

    /**
     * Get the current request method.
     */
    public function method(string $type = \null): string
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
    public function header(string $key = \null, string $default = \null): ?string
    {
        if (\is_null($key)) {
            return $this->headers;
        }

        return $this->headers[\strtoupper($key)] ?? $default;
    }

    /**
     * Retrieve an argument.
     */
    public function offsetGet($offset)
    {
        return $this->input($offset);
    }

    /**
     * Set argument (noop)
     */
    public function offsetSet($offset, $value): void
    {
    }

    /**
     * Check if an argument exists.
     */
    public function offsetExists($offset): bool
    {
        return isset($this->container[$offset]);
    }

    /**
     * Delete an argument (noop)
     */
    public function offsetUnset($offset): void
    {
    }

    /**
     * Get an argument (object-style)
     */
    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    /**
     * Set an argument (object-style)
     */
    public function __set($name, $value)
    {
        return $this->offsetSet($name, $value);
    }
}
