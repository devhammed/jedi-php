<?php

namespace Jedi;

use ArrayAccess;
use Jedi\Exceptions\Context\NotFoundException;

/**
 * Context Class
 *
 * @property-read \Jedi\Application $app
 * @property-read \Jedi\Router $router
 * @property-read \Jedi\Arguments $args
 * @property-read \Jedi\Request $request
 * @property-read \Jedi\Response $response
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
        'args',
        'router',
        'request',
        'response',
    ];

    /**
     * Creates a new application context.
     */
    public function __construct(Application $application)
    {
        $this->container['app'] = $application;
        $this->container['args'] = new Arguments();
        $this->container['request'] = new Request();
        $this->container['response'] = new Response();
    }

    /**
     * Get a property from context container.
     */
    public function offsetGet($offset)
    {
        if (!isset($this->container[$offset])) {
            throw new NotFoundException(sprintf(
                'Container key "%s" is not defined',
                $offset,
            ));
        }

        $value = $this->container[$offset];

        return \is_callable($value) ? \call_user_func($value, $this) : $value;
    }

    /**
     * Register a service.
     */
    public function offsetSet($offset, $value)
    {
        if ($this->isBuiltin($offset)) {
            return;
        }

        $this->container[$offset] = $value;
    }

    /**
     * Check if service exists in container.
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Delete an argument (noop)
     */
    public function offsetUnset($offset)
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
