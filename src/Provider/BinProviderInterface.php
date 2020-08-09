<?php

namespace Calculator\Provider;

use Calculator\Model\Country;
use Calculator\Model\Transaction;
use Calculator\Provider\Exception\InvalidBinDataException;

/**
 * Interface BinProviderInterface
 * @package Calculator\Provider
 */
interface BinProviderInterface
{
    /**
     * @param Transaction $transaction
     * @return Country
     * @throws InvalidBinDataException
     */
    public function getCountry(Transaction $transaction): Country;
}
