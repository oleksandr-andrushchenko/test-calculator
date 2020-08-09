<?php

namespace Calculator;

use Calculator\Model\Transaction;
use Calculator\Provider\BinProviderInterface;
use Calculator\Provider\DataProviderInterface;
use Calculator\Provider\Exception\InvalidBinDataException;
use Calculator\Provider\Exception\InvalidRateDataException;
use Calculator\Provider\RateProviderInterface;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * Class SimpleCalculator
 * @package Calculator
 */
class SimpleCalculator implements CalculatorInterface
{
    /**
     * @var DataProviderInterface
     */
    private $dataProvider;

    /**
     * @var BinProviderInterface
     */
    private $binProvider;

    /**
     * @var RateProviderInterface
     */
    private $rateProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $baseCurrency;

    /**
     * Calculator constructor.
     * @param object|DataProviderInterface $dataProvider
     * @param object|BinProviderInterface $binProvider
     * @param object|RateProviderInterface $rateProvider
     * @param object|LoggerInterface $logger
     * @param string $baseCurrency
     */
    public function __construct(
        DataProviderInterface $dataProvider,
        BinProviderInterface $binProvider,
        RateProviderInterface $rateProvider,
        LoggerInterface $logger,
        string $baseCurrency = 'EUR'
    ) {
        $this->dataProvider = $dataProvider;
        $this->binProvider = $binProvider;
        $this->rateProvider = $rateProvider;
        $this->logger = $logger;
        $this->baseCurrency = $baseCurrency;
    }

    /**
     * @inheritDoc
     */
    public function getCommissions(): array
    {
        $commissions = [];

        $transactions = $this->dataProvider->getTransactions();

        foreach ($transactions as $transaction) {
            try {
                $commissions[] = $this->getCommission($transaction);
            } catch (Exception $e) {
                $this->logger->error($e);
            }
        }

        return $commissions;
    }

    /**
     * @param Transaction $transaction
     * @return float
     * @throws InvalidBinDataException
     * @throws InvalidRateDataException
     */
    private function getCommission(Transaction $transaction): float
    {
        $country = $this->binProvider->getCountry($transaction);
        $rate = $this->rateProvider->getRate($transaction);

        $fixedAmount = $this->getFixedAmount($transaction, $rate);
        $coefficient = $country->isEu() ? 0.01 : 0.02;

        return $fixedAmount * $coefficient;
    }

    /**
     * @param Transaction $transaction
     * @param float|null $rate
     * @return float
     */
    private function getFixedAmount(Transaction $transaction, float $rate = null): float
    {
        if (!$rate) {
            return $transaction->getAmount();
        }

        if ($this->baseCurrency == $transaction->getCurrency()) {
            return $transaction->getAmount();
        }

        return $transaction->getAmount() / $rate;
    }
}