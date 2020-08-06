<?php

namespace Calculator\Provider;

use Calculator\Model\Country;
use Calculator\Provider\Exception\InvalidBinDataException;

/**
 * Interface BinProviderInterface
 * @package Calculator\Provider
 */
interface BinProviderInterface
{
    /**
     * @param int $bin
     * @return Country
     * @throws InvalidBinDataException
     */
    public function getCountry(int $bin): Country;
}
