<?php

namespace Jedi;

use ArrayAccess;

class Arguments implements ArrayAccess
{
    private ?array $container = \null;

    /**
     * Set arguments (this can only happens once per request).
     */
    public function setArgs(array $args = [])
    {
        if (\is_null($this->container)) {
            $this->container = $args;
        }
    }

    /**
     * Retrieve an argument.
     */
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->container[$offset] : \null;
    }

    /**
     * Set argument (noop)
     */
    public function offsetSet($offset, $value)
    {
    }

    /**
     * Check if an argument exists.
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
    }

    /**
     * Get an argument (object-style)
     */
    public function __get($name)
    {
        return $this->offsetGet($name);
    }
}
