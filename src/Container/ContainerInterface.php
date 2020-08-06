<?php

namespace Calculator\Container;

/**
 * Interface ContainerInterface
 * @package Calculator\Container
 */
interface ContainerInterface
{
    /**
     * @param string $key
     * @param mixed ...$vars
     * @return object|null
     */
    public function get(string $key, ...$vars): ?object;
}