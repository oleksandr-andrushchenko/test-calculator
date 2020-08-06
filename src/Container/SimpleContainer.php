<?php

namespace Calculator\Container;

/**
 * Class SimpleContainer
 * @package Calculator\Container
 */
class SimpleContainer implements ContainerInterface
{
    /**
     * @var callable[]
     */
    private $singletons;

    /**
     * @var object[]
     */
    private $instances;

    /**
     * SimpleContainer constructor.
     * @param callable[] $singletons
     */
    public function __construct(array $singletons = [])
    {
        $this->singletons = $singletons;
    }


    public function get(string $key, ...$vars): ?object
    {
        if (empty($this->singletons[$key])) {
            return null;
        }

        if (empty($this->instances[$key])) {
            $args = func_get_args();
            $args[0] = $this;
            $this->instances[$key] = $this->singletons[$key](...$args);
        }

        return $this->instances[$key];
    }
}