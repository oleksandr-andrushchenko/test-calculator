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
    public const DEFAULT_BASE_CURRENCY = 'EUR';
    public const DEFAULT_EU_COEFFICIENT = 0.01;
    public const DEFAULT_NON_EU_COEFFICIENT = 0.02;

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
     * @var float
     */
    private $euCoefficient;

    /**
     * @var float
     */
    private $nonEuCoefficient;

    /**
     * SimpleCalculator constructor.
     * @param object|DataProviderInterface $dataProvider
     * @param object|BinProviderInterface $binProvider
     * @param object|RateProviderInterface $rateProvider
     * @param object|LoggerInterface $logger
     * @param string $baseCurrency
     * @param float $euCoefficient
     * @param float $nonEuCoefficient
     */
    public function __construct(
        DataProviderInterface $dataProvider,
        BinProviderInterface $binProvider,
        RateProviderInterface $rateProvider,
        LoggerInterface $logger,
        string $baseCurrency = self::DEFAULT_BASE_CURRENCY,
        float $euCoefficient = self::DEFAULT_EU_COEFFICIENT,
        float $nonEuCoefficient = self::DEFAULT_NON_EU_COEFFICIENT
    ) {
        $this->dataProvider = $dataProvider;
        $this->binProvider = $binProvider;
        $this->rateProvider = $rateProvider;
        $this->logger = $logger;
        $this->baseCurrency = $baseCurrency;
        $this->euCoefficient = $euCoefficient;
        $this->nonEuCoefficient = $nonEuCoefficient;
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
        $fixedAmount = $this->getFixedAmount($transaction);

        $country = $this->binProvider->getCountry($transaction);

        $coefficient = $country->isEu() ? $this->euCoefficient : $this->nonEuCoefficient;

        return $fixedAmount * $coefficient;
    }

    /**
     * @param Transaction $transaction
     * @return float
     * @throws InvalidRateDataException
     */
    private function getFixedAmount(Transaction $transaction): float
    {
        $rate = $this->rateProvider->getRate($transaction);

        if (!$rate) {
            return $transaction->getAmount();
        }

        if ($this->baseCurrency == $transaction->getCurrency()) {
            return $transaction->getAmount();
        }

        return $transaction->getAmount() / $rate;
    }
}