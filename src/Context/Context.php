<?php

namespace Jedi\Context;

use ArrayAccess;
use Jedi\Request\Request;
use Jedi\Response\Response;
use Jedi\Application\Application;
use Jedi\Context\Exceptions\ServiceNotFoundException;

/**
 * Context Class
 *
 * @property-read \Jedi\Application\Application $app
 * @property-read \Jedi\Request\Request $request
 * @property-read \Jedi\Response\Response $response
 */
class Context implements ArrayAccess
{
    /**
     * Context container.
     */
    private array $container = [];

    /**
     * Builtin services.
     */
    private array $builtin = [
        'app',
        'request',
        'response',
    ];

    /**
     * Creates a new application context.
     */
    public function __construct(Application $application)
    {
        $this->container['app']      = $application;
        $this->container['request']  = new Request();
        $this->container['response'] = new Response($this->request);
    }

    /**
     * Get a property from context container.
     */
    public function offsetGet($offset)
    {
        if (!isset($this->container[$offset])) {
            throw new ServiceNotFoundException(sprintf(
                'Container key "%s" is not defined',
                $offset,
            ));
        }

        return $this->maybeCallable($this->container[$offset]);
    }

    /**
     * Register a service.
     */
    public function offsetSet($offset, $value): void
    {
        if ($this->isBuiltin($offset)) {
            return;
        }

        $this->container[$offset] = $this->maybeCallable($value);
    }

    /**
     * Check if service exists in container.
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
        if ($this->isBuiltin($offset)) {
            return;
        }

        unset($this->container[$offset]);
    }

    /**
     * Check if service is builtin.
     */
    protected function isBuiltin($name): bool
    {
        return \in_array($name, $this->builtin);
    }

    /**
     * Call a callable value if it is one.
     */
    protected function maybeCallable($value)
    {
        return \is_callable($value) ? \call_user_func($value, $this) : $value;
    }

    /**
     * Register a service.
     */
    public function __set(string $name, $value)
    {
        return $this->offsetSet($name, $value);
    }

    /**
     * Get a property from context container.
     */
    public function __get(string $name)
    {
        return $this->offsetGet($name);
    }

    /**
     * Check if property exists in container.
     */
    public function __isset($name)
    {
        return $this->offsetExists($name);
    }
}
