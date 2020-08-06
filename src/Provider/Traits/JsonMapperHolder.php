<?php

namespace Calculator\Provider\Traits;

use JsonMapper;

/**
 * Trait JsonMapperHolder
 * @package Calculator\Provider\Traits
 */
trait JsonMapperHolder
{
    /**
     * @var JsonMapper
     */
    private $jsonMapper;

    /**
     * JsonMapperHolder constructor.
     * @param object|JsonMapper $jsonMapper
     */
    public function __construct(JsonMapper $jsonMapper)
    {
        $this->jsonMapper = $jsonMapper;
    }
}
