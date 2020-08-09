<?php

namespace Calculator\Provider;

use Calculator\Model\Transaction;
use Calculator\Provider\Exception\InvalidRateDataException;

/**
 * Interface RateProviderInterface
 * @package Calculator\Provider
 */
interface RateProviderInterface
{
    /**
     * @param Transaction $transaction
     * @return float|null
     * @throws InvalidRateDataException
     */
    public function getRate(Transaction $transaction): ?float;
}