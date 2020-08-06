<?php

namespace Calculator\Provider;

use Calculator\Model\Transaction;

/**
 * Interface DataProviderInterface
 * @package Calculator\Provider
 */
interface DataProviderInterface
{
    /**
     * @return Transaction[]
     */
    public function getTransactions(): array;
}