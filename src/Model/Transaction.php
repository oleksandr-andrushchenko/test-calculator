<?php

namespace Calculator\Model;

/**
 * Class Transaction
 * @package Calculator\Model
 */
class Transaction
{
    /**
     * @var string
     */
    private $currency;

    /**
     * @var float
     */
    private $amount;

    /**
     * @var int
     */
    private $bin;

    /**
     * Transaction constructor.
     * @param string $currency
     * @param float $amount
     * @param int $bin
     */
    public function __construct(string $currency, float $amount, int $bin)
    {
        $this->currency = $currency;
        $this->amount = $amount;
        $this->bin = $bin;
    }

    /**
     * @inheritDoc
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @inheritDoc
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @inheritDoc
     */
    public function getBin(): int
    {
        return $this->bin;
    }
}
