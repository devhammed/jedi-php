<?php

namespace Jedi\Response;

class TransformedResponse
{
    /**
     * Data container.
     */
    protected $container;

    /**
     * Creates a new Jedi transformed response instance.
     */
    public function __construct($data)
    {
        $this->container = $data;
    }

    /**
     * Returns the transformed data.
     */
    public function __toString(): string
    {
        return $this->container;
    }
}
