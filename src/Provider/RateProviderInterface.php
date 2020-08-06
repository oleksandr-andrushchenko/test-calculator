<?php

namespace Calculator\Provider;

use Calculator\Provider\Exception\InvalidRateDataException;

/**
 * Interface RateProviderInterface
 * @package Calculator\Provider
 */
interface RateProviderInterface
{
    /**
     * @param string $currency
     * @return float|null
     * @throws InvalidRateDataException
     */
    public function getRate(string $currency): ?float;
}