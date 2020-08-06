<?php

namespace Calculator\Provider\DTO;

/**
 * Class TransactionDTO
 * @package Calculator\Provider\DTO
 */
class TransactionDTO
{
    /**
     * @var string
     */
    public $currency;

    /**
     * @var float
     */
    public $amount;

    /**
     * @var int
     */
    public $bin;
}
